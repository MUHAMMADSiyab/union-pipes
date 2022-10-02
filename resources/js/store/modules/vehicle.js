import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_VEHICLES,
    UPDATE_VEHICLE,
    DELETE_VEHICLE,
    DELETE_VEHICLES,
    GET_VEHICLE,
    NEW_VEHICLE,
    GET_VEHICLE_CHAMBERS,
} from "../../mutation_constants";

const state = {
    vehicles: [],
    vehicle_chambers: [],
    vehicle: null,
};

const getters = {
    vehicles: (state) => state.vehicles,
    vehicle_chambers: (state) => state.vehicle_chambers,
    vehicle: (state) => state.vehicle,
};

const actions = {
    // Add vehicle
    async addVehicle({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/vehicles", data);

            commit(NEW_VEHICLE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Vehicle added successfully",
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

    // Get vehicles
    async getVehicles({ commit }) {
        try {
            const res = await axios.get("/api/vehicles");

            commit(SET_LOADING, false, { root: true });
            commit(GET_VEHICLES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get vehicle chambers
    async getVehicleChambers({ commit }, id) {
        try {
            const res = await axios.get(`/api/vehicles/${id}/chambers`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_VEHICLE_CHAMBERS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single vehicle
    async getVehicle({ commit }, vehicleId) {
        try {
            const res = await axios.get(`/api/vehicles/${vehicleId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_VEHICLE, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update vehicle
    async updateVehicle({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/vehicles/${data.id}`, data);

            commit(UPDATE_VEHICLE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Vehicle updated successfully",
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

    // Delete vehicle
    async deleteVehicle({ dispatch, commit }, vehicleId) {
        try {
            const res = await axios.delete(`/api/vehicles/${vehicleId}`);

            commit(DELETE_VEHICLE, vehicleId);

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

    // Delete multiple vehicles
    async deleteMultipleVehicles({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/vehicles/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_VEHICLES, ids);

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
    GET_VEHICLES: (state, payload) => (state.vehicles = payload),

    GET_VEHICLE_CHAMBERS: (state, payload) =>
        (state.vehicle_chambers = payload),

    GET_VEHICLE: (state, payload) => (state.vehicle = payload),

    NEW_VEHICLE: (state, payload) => {
        state.vehicles.unshift(payload);
    },

    UPDATE_VEHICLE: (state, payload) => {
        const index = state.vehicles.findIndex(
            (vehicle) => vehicle.id === payload.id
        );
        state.vehicles.splice(index, 1, payload);
    },

    DELETE_VEHICLE: (state, payload) => {
        const index = state.vehicles.findIndex(
            (vehicle) => vehicle.id === payload
        );
        state.vehicles.splice(index, 1);
    },

    DELETE_VEHICLES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.vehicles.findIndex(
                (vehicle) => vehicle.id === id
            );
            state.vehicles.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
