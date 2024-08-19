import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_GATE_PASS,
    GET_GATE_PASSES,
    GET_GATE_PASS,
    UPDATE_GATE_PASS,
    DELETE_GATE_PASS,
    DELETE_GATE_PASSES,
    GET_NO_SELL_GATE_PASSES,
} from "../../mutation_constants";

const state = {
    gate_passes: [],
    no_sell_gate_passes: [],
    gate_pass: null,
    new_gate_pass: null,
};

const getters = {
    gate_passes: (state) => state.gate_passes,
    no_sell_gate_passes: (state) => state.no_sell_gate_passes,
    gate_pass: (state) => state.gate_pass,
    new_gate_pass: (state) => state.new_gate_pass,
};

const actions = {
    // Add gate pass
    async addGatePass({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/gate_passes", data);

            commit(NEW_GATE_PASS, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Gate Pass created successfully",
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

    // Get gate passes
    async getGatePasses({ commit }) {
        try {
            const res = await axios.get("/api/gate_passes");

            commit(SET_LOADING, false, { root: true });
            commit(GET_GATE_PASSES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    async getNoSellGatePasses({ commit }) {
        try {
            const res = await axios.get("/api/no_sell_gate_passes");

            commit(SET_LOADING, false, { root: true });
            commit(GET_NO_SELL_GATE_PASSES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single gate pass
    async getGatePass({ commit }, gate_passId) {
        try {
            const res = await axios.get(`/api/gate_passes/${gate_passId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_GATE_PASS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update gate pass
    async updateGatePass({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/gate_passes/${data.id}`, data);

            commit(UPDATE_GATE_PASS, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "GatePass updated successfully",
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

    // Delete gate pass
    async deleteGatePass({ dispatch, commit }, gate_passId) {
        try {
            const res = await axios.delete(`/api/gate_passes/${gate_passId}`);

            commit(DELETE_GATE_PASS, gate_passId);

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

    // Delete multiple gate passes
    async deleteMultipleGatePasses({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/gate_passes/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_GATE_PASSES, ids);

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
    GET_GATE_PASSES: (state, payload) => (state.gate_passes = payload),

    GET_NO_SELL_GATE_PASSES: (state, payload) =>
        (state.no_sell_gate_passes = payload),

    GET_GATE_PASS: (state, payload) => (state.gate_pass = payload),

    NEW_GATE_PASS: (state, payload) => {
        state.gate_passes.unshift(payload);
        state.new_gate_pass = payload;
    },

    UPDATE_GATE_PASS: (state, payload) => {
        const index = state.gate_passes.findIndex(
            (gate_pass) => gate_pass.id === payload.id
        );
        state.gate_passes.splice(index, 1, payload);
    },

    DELETE_GATE_PASS: (state, payload) => {
        const index = state.gate_passes.findIndex(
            (gate_pass) => gate_pass.id === payload
        );
        state.gate_passes.splice(index, 1);
    },

    DELETE_GATE_PASSES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.gate_passes.findIndex(
                (gate_pass) => gate_pass.id === id
            );
            state.gate_passes.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
