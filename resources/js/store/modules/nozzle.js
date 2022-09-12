import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_NOZZLES,
    UPDATE_NOZZLE,
    DELETE_NOZZLE,
    DELETE_NOZZLES,
    GET_NOZZLE,
    NEW_NOZZLE,
} from "../../mutation_constants";

const state = {
    nozzles: [],
    nozzle: null,
};

const getters = {
    nozzles: (state) => state.nozzles,
    nozzle: (state) => state.nozzle,
};

const actions = {
    // Add nozzle
    async addNozzle({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/nozzles", data);

            commit(NEW_NOZZLE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Nozzle added successfully",
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

    // Get nozzles
    async getNozzles({ commit }) {
        try {
            const res = await axios.get("/api/nozzles");

            commit(SET_LOADING, false, { root: true });
            commit(GET_NOZZLES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single nozzle
    async getNozzle({ commit }, nozzleId) {
        try {
            const res = await axios.get(`/api/nozzles/${nozzleId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_NOZZLE, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update nozzle
    async updateNozzle({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/nozzles/${data.id}`, data);

            commit(UPDATE_NOZZLE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Nozzle updated successfully",
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

    // Delete nozzle
    async deleteNozzle({ dispatch, commit }, nozzleId) {
        try {
            const res = await axios.delete(`/api/nozzles/${nozzleId}`);

            commit(DELETE_NOZZLE, nozzleId);

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

    // Delete multiple nozzles
    async deleteMultipleNozzles({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/nozzles/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_NOZZLES, ids);

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
    GET_NOZZLES: (state, payload) => (state.nozzles = payload),

    GET_NOZZLE: (state, payload) => (state.nozzle = payload),

    NEW_NOZZLE: (state, payload) => {
        state.nozzles.unshift(payload);
    },

    UPDATE_NOZZLE: (state, payload) => {
        const index = state.nozzles.findIndex(
            (nozzle) => nozzle.id === payload.id
        );
        state.nozzles.splice(index, 1, payload);
    },

    DELETE_NOZZLE: (state, payload) => {
        const index = state.nozzles.findIndex(
            (nozzle) => nozzle.id === payload
        );
        state.nozzles.splice(index, 1);
    },

    DELETE_NOZZLES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.nozzles.findIndex((nozzle) => nozzle.id === id);
            state.nozzles.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
