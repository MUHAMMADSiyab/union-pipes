import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_PRODUCTION,
    GET_PRODUCTIONS,
    GET_PRODUCTION,
    UPDATE_PRODUCTION,
    DELETE_PRODUCTION,
    DELETE_PRODUCTIONS,
} from "../../mutation_constants";
import { sortBy } from "lodash";

const state = {
    productions: [],
    production: null,
    total: 0,
    loading: false,
};

const getters = {
    productions: (state) => state.productions,
    production: (state) => state.production,
    total: (state) => state.total,
    loading: (state) => state.loading,
};

const actions = {
    // Add production
    async addProduction({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/productions", data);

            commit(NEW_PRODUCTION, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Production added successfully",
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }

            console.log(error);
        }
    },

    // Get productions
    async getProductions({ commit }, { page, itemsPerPage, sortBy, sortDesc }) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "date";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/productions?page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_PRODUCTIONS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Search productions
    async searchProductions(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc, search }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "date";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/search_productions?search=${search}&page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_PRODUCTIONS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Get a single production
    async getProduction({ commit }, productionId) {
        try {
            const res = await axios.get(`/api/productions/${productionId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PRODUCTION, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update production
    async updateProduction({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/productions/${data.id}`, data);

            commit(UPDATE_PRODUCTION, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Production updated successfully",
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
            console.log(error);
        }
    },

    // Delete production
    async deleteProduction({ dispatch, commit }, productionId) {
        try {
            const res = await axios.delete(`/api/productions/${productionId}`);

            commit(DELETE_PRODUCTION, productionId);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },

    // Delete multiple productions
    async deleteMultipleProductions({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/productions/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_PRODUCTIONS, ids);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },
};

const mutations = {
    SET_LOADING: (state, payload) => (state.loading = payload),

    GET_PRODUCTIONS: (state, payload) => {
        state.productions = payload.data;
        state.total = payload.total;
    },

    GET_PRODUCTION: (state, payload) => (state.production = payload),

    NEW_PRODUCTION: (state, payload) => {
        state.productions.unshift(payload);
    },

    UPDATE_PRODUCTION: (state, payload) => {
        const index = state.productions.findIndex(
            (production) => production.id === payload.id
        );
        state.productions.splice(index, 1, payload);
    },

    DELETE_PRODUCTION: (state, payload) => {
        const index = state.productions.findIndex(
            (production) => production.id === payload
        );
        state.productions.splice(index, 1);
    },

    DELETE_PRODUCTIONS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.productions.findIndex(
                (production) => production.id === id
            );
            state.productions.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
