<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSurveyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'image_preview' => $this->image ? URL::to($this->image) : null,
            'status' => $this->status !== 'draft',
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'expire_date' => (new DateTime($this->expire_at))->format('Y-m-d H:i:s'),
            'questions' => $this->questions()->count(),
            'answers' => $this->answers()->count(),
        ];
    }
}
