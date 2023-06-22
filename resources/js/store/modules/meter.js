import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_METERS,
    UPDATE_METER,
    DELETE_METER,
    DELETE_METERS,
    GET_METER,
    NEW_METER,
    GET_DETAILED_METERS,
} from "../../mutation_constants";

const state = {
    meters: [],
    detailed_meters: [],
    meter: null,
};

const getters = {
    meters: (state) => state.meters,
    detailed_meters: (state) => state.detailed_meters,
    meter: (state) => state.meter,
};

const actions = {
    // Add meter
    async addMeter({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/meters", data);

            commit(NEW_METER, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Meter added successfully",
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

    // Get meters
    async getMeters({ commit }) {
        try {
            const res = await axios.get(`/api/meters`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_METERS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get detailed meters (with dispenser -> tank -> product)
    async getDetailedMeters({ commit }) {
        try {
            const res = await axios.get(`/api/meters/detailed_meters`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_DETAILED_METERS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single meter
    async getMeter({ commit }, meterId) {
        try {
            const res = await axios.get(`/api/meters/${meterId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_METER, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update meter
    async updateMeter({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/meters/${data.id}`, data);

            commit(UPDATE_METER, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Meter updated successfully",
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

    // Delete meter
    async deleteMeter({ dispatch, commit }, meterId) {
        try {
            const res = await axios.delete(`/api/meters/${meterId}`);

            commit(DELETE_METER, meterId);

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

    // Delete multiple meters
    async deleteMultipleMeters({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/meters/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_METERS, ids);

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
    GET_METERS: (state, payload) => (state.meters = payload),

    GET_DETAILED_METERS: (state, payload) => (state.detailed_meters = payload),

    GET_METER: (state, payload) => (state.meter = payload),

    NEW_METER: (state, payload) => {
        state.meters.unshift(payload);
    },

    UPDATE_METER: (state, payload) => {
        const index = state.meters.findIndex(
            (meter) => meter.id === payload.id
        );
        state.meters.splice(index, 1, payload);
    },

    DELETE_METER: (state, payload) => {
        const index = state.meters.findIndex((meter) => meter.id === payload);
        state.meters.splice(index, 1);
    },

    DELETE_METERS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.meters.findIndex((meter) => meter.id === id);
            state.meters.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
