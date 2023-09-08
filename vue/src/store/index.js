import { createStore } from 'vuex';
import axiosClient from '../axios';

const store = createStore({
    state: {
        user: {
            data: {},
            token: sessionStorage.getItem('TOKEN')
        },
        currentSurvey: {
            loading: false,
            data: {}
        },
        surveys: {
            loading: false,
            links: [],
            data: []
        },
        questionTypes: ['radio', 'text', 'checkbox', 'textarea', 'select'],
        notification: {
            show: false,
            type: null,
            message: null
        },
        dashboard: {
            loading: false,
            data: {}
        }
    },
    getters: {},
    modules: {},
    actions: {
        register({ commit }, user) {
            return axiosClient.post('/register', user)
                .then(({ data }) => {
                    commit('setUser', data)
                    return data;
                })
        },
        login({ commit }, user) {
            return axiosClient.post('/login', user)
                .then(({ data }) => {
                    commit('setUser', data)
                    return data;
                })
        },
        logout({ commit }) {
            return axiosClient.post('/logout')
                .then((response) => {
                    commit('logoutUser')
                    return response;
                })
        },
        saveSurvey({ commit }, survey) {
            delete survey.image_preview;
            let response;
            if (survey.id) {
                response = axiosClient
                    .put(`/survey/${survey.id}`, survey)
                    .then((res) => {
                        commit('setCurrentSurvey', res.data)
                        return res;
                    })
            } else {
                response = axiosClient
                    .post('/survey', survey)
                    .then((res) => {
                        commit('setCurrentSurvey', res.data)
                        return res;
                    })
            }
        },
        getSurvey({ commit }, id) {
            commit('setCurrentSurveyLoading', true)
            return axiosClient
                .get(`/survey/${id}`)
                .then((res) => {
                    commit('setCurrentSurvey', res.data);
                    commit('setCurrentSurveyLoading', false);
                    return res;
                }).catch((error) => {
                    commit('setCurrentSurveyLoading', false);
                    throw error;
                })
        },
        deleteSurvey({ }, id) {
            return axiosClient.delete(`/survey/${id}`)
        },
        getSurveys({ commit }, { url = null } = {}) {
            url = url || '/survey'
            commit("setSurveysLoading", true);
            return axiosClient.get(url).then((res) => {
                commit("setSurveysLoading", false);
                commit("setSurveys", res.data);
                return res
            })
        },
        getSurveyBySlug({ commit }, slug) {
            commit("setCurrentSurveyLoading", true)
            return axiosClient.get(`/survey-by-slug/${slug}`).then((res) => {
                commit("setCurrentSurveyLoading", false)
                commit("setCurrentSurvey", res.data)
                return res;
            }).catch((err) => {
                commit("setCurrentSurveyLoading", false);
                throw err;
            })
        },
        saveSurveyAnswer({ commit }, { surveyId, answers }) {
            return axiosClient.post(`/survey/${surveyId}/answer`, {answers})
        },
        getDashboardData({ commit }) {
            commit("setDashboardLoading", true)
            return axiosClient.get('/dashboard')
                .then((res) => {
                    commit("setDashboardLoading", false)
                    commit("setDashboardData", res.data)
                    return res
                }).catch((err) => {
                    commit("setDashboardLoading", false)
                    return err
                })
        }
    },
    mutations: {
        logoutUser: (state) => {
            state.user.data = {},
                state.user.token = null
            sessionStorage.removeItem('TOKEN')
        },
        setUser: (state, userData) => {
            state.user.token = userData.token;
            state.user.data = userData.user;
            sessionStorage.setItem('TOKEN', userData.token)
        },
        setCurrentSurveyLoading: (state, loading) => {
            state.currentSurvey.loading = loading;
        },
        setCurrentSurvey: (state, survey) => {
            state.currentSurvey.data = survey.data
        },
        setDashboardLoading: (state, loading) => {
            state.dashboard.loading = loading;
        },
        setDashboardData: (state, data) => {
            state.dashboard.data = data
        },
        setSurveys: (state, surveys) => {
            state.surveys.data = surveys.data;
            state.surveys.links = surveys.meta.links;
        },
        setSurveysLoading: (state, loading) => {
            state.surveys.loading = loading;
        },
        notify: (state, { message, type }) => {
            state.notification.show = true;
            state.notification.type = type;
            state.notification.message = message;
            setTimeout(() => {
                state.notification.show = false;
            }, 3000)
        }
    },
});

export default store;
