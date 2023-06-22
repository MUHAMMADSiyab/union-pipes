import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_MACHINE,
    GET_MACHINES,
    GET_MACHINE,
    UPDATE_MACHINE,
    DELETE_MACHINE,
    DELETE_MACHINES,
} from "../../mutation_constants";

const state = {
    machines: [],
    machine: null,
};

const getters = {
    machines: (state) => state.machines,
    machine: (state) => state.machine,
};

const actions = {
    // Add machine
    async addMachine({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/machines", data);

            commit(NEW_MACHINE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Machine added successfully",
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

    // Get machines
    async getMachines({ commit }) {
        try {
            const res = await axios.get("/api/machines");

            commit(SET_LOADING, false, { root: true });
            commit(GET_MACHINES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single machine
    async getMachine({ commit }, machineId) {
        try {
            const res = await axios.get(`/api/machines/${machineId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_MACHINE, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update machine
    async updateMachine({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/machines/${data.id}`, data);

            commit(UPDATE_MACHINE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Machine updated successfully",
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

    // Delete machine
    async deleteMachine({ dispatch, commit }, machineId) {
        try {
            const res = await axios.delete(`/api/machines/${machineId}`);

            commit(DELETE_MACHINE, machineId);

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

    // Delete multiple machines
    async deleteMultipleMachines({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/machines/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_MACHINES, ids);

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
    GET_MACHINES: (state, payload) => (state.machines = payload),

    GET_MACHINE: (state, payload) => (state.machine = payload),

    NEW_MACHINE: (state, payload) => {
        state.machines.unshift(payload);
    },

    UPDATE_MACHINE: (state, payload) => {
        const index = state.machines.findIndex(
            (machine) => machine.id === payload.id
        );
        state.machines.splice(index, 1, payload);
    },

    DELETE_MACHINE: (state, payload) => {
        const index = state.machines.findIndex(
            (machine) => machine.id === payload
        );
        state.machines.splice(index, 1);
    },

    DELETE_MACHINES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.machines.findIndex(
                (machine) => machine.id === id
            );
            state.machines.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
