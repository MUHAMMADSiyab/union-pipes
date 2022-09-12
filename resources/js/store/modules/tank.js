import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_TANKS,
    UPDATE_TANK,
    DELETE_TANK,
    DELETE_TANKS,
    GET_TANK,
    NEW_TANK,
} from "../../mutation_constants";

const state = {
    tanks: [],
    tank: null,
};

const getters = {
    tanks: (state) => state.tanks,
    tank: (state) => state.tank,
};

const actions = {
    // Add tank
    async addTank({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/tanks", data);

            commit(NEW_TANK, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Tank added successfully",
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

    // Get tanks
    async getTanks({ commit }) {
        try {
            const res = await axios.get("/api/tanks");

            commit(SET_LOADING, false, { root: true });
            commit(GET_TANKS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single tank
    async getTank({ commit }, tankId) {
        try {
            const res = await axios.get(`/api/tanks/${tankId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_TANK, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update tank
    async updateTank({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/tanks/${data.id}`, data);

            commit(UPDATE_TANK, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Tank updated successfully",
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

    // Delete tank
    async deleteTank({ dispatch, commit }, tankId) {
        try {
            const res = await axios.delete(`/api/tanks/${tankId}`);

            commit(DELETE_TANK, tankId);

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

    // Delete multiple tanks
    async deleteMultipleTanks({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/tanks/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_TANKS, ids);

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
    GET_TANKS: (state, payload) => (state.tanks = payload),

    GET_TANK: (state, payload) => (state.tank = payload),

    NEW_TANK: (state, payload) => {
        state.tanks.unshift(payload);
    },

    UPDATE_TANK: (state, payload) => {
        const index = state.tanks.findIndex((tank) => tank.id === payload.id);
        state.tanks.splice(index, 1, payload);
    },

    DELETE_TANK: (state, payload) => {
        const index = state.tanks.findIndex((tank) => tank.id === payload);
        state.tanks.splice(index, 1);
    },

    DELETE_TANKS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.tanks.findIndex((tank) => tank.id === id);
            state.tanks.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
