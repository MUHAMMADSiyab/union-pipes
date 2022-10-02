import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_UTILITIES,
    UPDATE_UTILITY,
    DELETE_UTILITY,
    DELETE_UTILITIES,
    GET_UTILITY,
    NEW_UTILITY,
    OLD_UTILITY,
} from "../../mutation_constants";

const state = {
    utilities: [],
    utility: null,
    recent_utility: null,
    old_utility: null,
};

const getters = {
    utilities: (state) => state.utilities,
    utility: (state) => state.utility,
    recent_utility: (state) => state.recent_utility,
    old_utility: (state) => state.old_utility,
};

const actions = {
    // Add utility
    async addUtility({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/utilities", data);

            commit(NEW_UTILITY, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }

            console.log(error);
        }
    },

    // Get utilities
    async getUtilities({ commit }) {
        try {
            const res = await axios.get("/api/utilities");

            commit(SET_LOADING, false, { root: true });
            commit(GET_UTILITIES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single utility
    async getUtility({ commit }, vehicleAdjustmentId) {
        try {
            const res = await axios.get(
                `/api/utilities/${vehicleAdjustmentId}`
            );

            commit(SET_LOADING, false, { root: true });
            commit(GET_UTILITY, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update utility
    async updateUtility({ commit }, data) {
        try {
            const res = await axios.put(`/api/utilities/${data.id}`, data);

            commit(UPDATE_UTILITY, res.data.updated_utility);
            commit(OLD_UTILITY, res.data.old_utility);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
            console.log(error);
        }
    },

    // Delete utility
    async deleteUtility({ dispatch, commit }, utilityId) {
        try {
            const res = await axios.delete(`/api/utilities/${utilityId}`);

            commit(DELETE_UTILITY, utilityId);

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

    // Delete multiple utilities
    async deleteMultipleUtilities({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/utilities/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_UTILITIES, ids);

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
    GET_UTILITIES: (state, payload) => (state.utilities = payload),

    GET_UTILITY: (state, payload) => (state.utility = payload),

    NEW_UTILITY: (state, payload) => {
        state.recent_utility = payload;
    },

    OLD_UTILITY: (state, payload) => {
        state.old_utility = payload;
    },

    UPDATE_UTILITY: (state, payload) => {
        const index = state.utilities.findIndex(
            (utility) => utility.id === payload.id
        );
        state.utilities.splice(index, 1, payload);
    },

    DELETE_UTILITY: (state, payload) => {
        const index = state.utilities.findIndex(
            (utility) => utility.id === payload
        );
        state.utilities.splice(index, 1);
    },

    DELETE_UTILITIES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.utilities.findIndex(
                (utility) => utility.id === id
            );
            state.utilities.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
