import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_DISPENSER,
    GET_DISPENSERS,
    GET_DISPENSER,
    UPDATE_DISPENSER,
    DELETE_DISPENSER,
    DELETE_DISPENSERS,
} from "../../mutation_constants";

const state = {
    dispensers: [],
    dispenser: null,
};

const getters = {
    dispensers: (state) => state.dispensers,
    dispenser: (state) => state.dispenser,
};

const actions = {
    // Add dispenser
    async addDispenser({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/dispensers", data);

            commit(NEW_DISPENSER, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Dispenser added successfully",
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

    // Get dispensers
    async getDispensers({ commit }) {
        try {
            const res = await axios.get("/api/dispensers");

            commit(SET_LOADING, false, { root: true });
            commit(GET_DISPENSERS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single dispenser
    async getDispenser({ commit }, dispenserId) {
        try {
            const res = await axios.get(`/api/dispensers/${dispenserId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_DISPENSER, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update dispenser
    async updateDispenser({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/dispensers/${data.id}`, data);

            commit(UPDATE_DISPENSER, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Dispenser updated successfully",
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

    // Delete dispenser
    async deleteDispenser({ dispatch, commit }, dispenserId) {
        try {
            const res = await axios.delete(`/api/dispensers/${dispenserId}`);

            commit(DELETE_DISPENSER, dispenserId);

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

    // Delete multiple dispensers
    async deleteMultipleDispensers({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/dispensers/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_DISPENSERS, ids);

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
    GET_DISPENSERS: (state, payload) => (state.dispensers = payload),

    GET_DISPENSER: (state, payload) => (state.dispenser = payload),

    NEW_DISPENSER: (state, payload) => {
        state.dispensers.unshift(payload);
    },

    UPDATE_DISPENSER: (state, payload) => {
        const index = state.dispensers.findIndex(
            (dispenser) => dispenser.id === payload.id
        );
        state.dispensers.splice(index, 1, payload);
    },

    DELETE_DISPENSER: (state, payload) => {
        const index = state.dispensers.findIndex(
            (dispenser) => dispenser.id === payload
        );
        state.dispensers.splice(index, 1);
    },

    DELETE_DISPENSERS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.dispensers.findIndex(
                (dispenser) => dispenser.id === id
            );
            state.dispensers.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
