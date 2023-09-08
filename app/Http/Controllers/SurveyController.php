<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnswerSurveyRequest;
use Exception;
use App\Models\Survey;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\SurveyResource;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionAnswer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return SurveyResource::collection(Survey::where('user_id', $user->id)->paginate(2));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();

        // Check if data has image
        if (isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }

        $survey = Survey::create($data);

        // Implement saving and storing survey questions
        foreach ($data['questions'] as $question) {
            $question['survey_id'] = $survey->id;
            $this->createQuestion($question);
        }

        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     */
    public function show(Survey $survey, Request $request)
    {
        $user = $request->user();

        if ($user->id !== $survey->user_id) {
            abort(403, 'Unauthorized Action!!!');
        }

        return new SurveyResource($survey);
    }

    public function showForGuest(Survey $survey)
    {
        return new SurveyResource($survey);
    }

    public function storeAnswer(StoreAnswerSurveyRequest $request, Survey $survey)
    {
        $validated = $request->validated();

        $surveyAnswer = SurveyAnswer::create([
            'survey_id' => $survey->id,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s')
        ]);

        foreach ($validated['answers'] as $questionId => $answer) {
            $question = SurveyQuestion::where(['id' => $questionId, 'survey_id' => $survey->id])->get();
            if(!$question) {
                return response("Invalid question ID: \"$questionId\"", 400);
            }

            $data = [
                'survey_question-id' => $questionId,
                'survey_answer_id' => $surveyAnswer->id,
                'answer' => is_array($answer) ? json_encode($answer) : $answer
            ];

            SurveyQuestionAnswer::create($data);
        }

        return response('', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $data = $request->validated();

        // Check if image exists
        if (isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            // Delete old image
            if ($survey->image) {
                $absolutePath = public_path($survey->image);
                File::delete($absolutePath);
            }
        }

        $survey->update($data);

        // Get ids of existing questions as plain array
        $existingIds = $survey->questions()->pluck('id')->toArray();

        // Get ids of new questions as plain array
        $newIds = Arr::pluck($data['questions'], 'id');

        // Find questions to delete
        $toDelete = array_diff($existingIds, $newIds);

        // Find questions to add
        $toAdd = array_diff($newIds, $existingIds);

        // Delete questions
        SurveyQuestion::destroy($toDelete);

        // Create new questions
        foreach ($data['questions'] as $question) {
            if (in_array($question['id'], $toAdd)) {
                $question['survey_id'] = $survey->id;
                $this->createQuestion($question);
            }
        }

        // Update existing questions
        $questionMap = collect($data['questions'])->keyBy('id');

        foreach ($survey->questions as $question) {
            if (isset($questionMap[$question->id])) {
                $this->updateQuestion($question, $questionMap[$question->id]);
            }
        }


        return new SurveyResource($survey);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey, Request $request)
    {
        $user = $request->user();

        if ($user->id !== $survey->user_id) {
            abort(403, 'Unauthorized Action!!!');
        }

        $survey->delete();

        // Delete old image
        if ($survey->image) {
            $absolutePath = public_path($survey->image);
            File::delete($absolutePath);
        }

        return response('', 204);
    }

    private function saveImage($image)
    {
        // Check if image is a valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            // Take everything after the comma and save to image variable
            $image = substr($image, strpos($image, ',') + 1);

            // Get the file extension
            $type = strtolower($type[1]); // png, jpg,

            // Check if file is a valid image
            if (!in_array($type, ['png', 'jpg', 'gif', 'jpeg'])) {
                throw new Exception("Please choose a valid image format('jpg', 'png', 'jpeg', 'gif')");
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new Exception("base64_decode failed");
            }
        } else {
            throw new Exception('Image is not a valid base64 string');
        }

        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;

        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }

        file_put_contents($relativePath, $image);

        return $relativePath;
    }

    private function createQuestion($data)
    {
        // Encode the data to save in database (we cannot save array in database)
        if (is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }

        $validator = Validator::make($data, [
            'question' => 'required|string',
            'type' => [
                'required',
                Rule::in([
                    Survey::TYPE_CHECKBOX,
                    Survey::TYPE_RADIO,
                    Survey::TYPE_SELECT,
                    Survey::TYPE_TEXT,
                    Survey::TYPE_TEXTAREA
                ])
            ],
            'description' => 'nullable|string',
            'data' => 'present',
            'survey_id' => 'exists:App\Models\Survey,id'
        ]);

        return SurveyQuestion::create($validator->validated());
    }

    private function updateQuestion(SurveyQuestion $question, $data)
    {
        if (is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }

        $validator = Validator::make($data, [
            'id' => 'exists:App\Models\SurveyQuestion,id',
            'type' => [
                'required',
                Rule::in([
                    Survey::TYPE_CHECKBOX,
                    Survey::TYPE_RADIO,
                    Survey::TYPE_SELECT,
                    Survey::TYPE_TEXT,
                    Survey::TYPE_TEXTAREA
                ])
            ],
            'question' => 'required|string',
            'description' => 'nullable|string',
            'data' => 'present'
        ]);

        return $question->update($validator->validated());
    }
}
