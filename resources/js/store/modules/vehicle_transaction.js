import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_VEHICLE_TRANSACTIONS,
    UPDATE_VEHICLE_TRANSACTION,
    DELETE_VEHICLE_TRANSACTION,
    DELETE_VEHICLE_TRANSACTIONS,
    GET_VEHICLE_TRANSACTION,
    NEW_VEHICLE_TRANSACTION,
} from "../../mutation_constants";

const state = {
    vehicle_transactions: [],
    vehicle_transaction: null,
};

const getters = {
    vehicle_transactions: (state) => state.vehicle_transactions,
    vehicle_transaction: (state) => state.vehicle_transaction,
};

const actions = {
    // Add vehicle_transaction
    async addVehicleTransaction({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/vehicle_transactions", data);

            commit(NEW_VEHICLE_TRANSACTION, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            if (!data.receive_payment) {
                return dispatch(
                    "alert/setAlert",
                    {
                        type: "success",
                        message: "VehicleTransaction entry added successfully",
                    },
                    { root: true }
                );
            }
        } catch (error) {
            console.log(error);
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
        }
    },

    // Get vehicle_transactions
    async getVehicleTransactions({ commit }, vehicleId) {
        try {
            const res = await axios.get(
                `/api/vehicle_transactions/get_vehicle_transactions/${vehicleId}`
            );

            commit(SET_LOADING, false, { root: true });
            commit(GET_VEHICLE_TRANSACTIONS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single vehicle_transaction
    async getVehicleTransaction({ commit }, vehicle_transactionId) {
        try {
            const res = await axios.get(
                `/api/vehicle_transactions/${vehicle_transactionId}`
            );

            commit(SET_LOADING, false, { root: true });
            commit(GET_VEHICLE_TRANSACTION, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update vehicle_transaction
    async updateVehicleTransaction({ dispatch, commit }, data) {
        try {
            const res = await axios.put(
                `/api/vehicle_transactions/${data.id}`,
                data
            );

            commit(
                UPDATE_VEHICLE_TRANSACTION,
                res.data.updated_vehicle_transaction
            );
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            if (!data.receive_payment) {
                return dispatch(
                    "alert/setAlert",
                    {
                        type: "success",
                        message:
                            "VehicleTransaction entry updated successfully",
                    },
                    { root: true }
                );
            }
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
            console.log(error);
        }
    },

    // Delete vehicle_transaction
    async deleteVehicleTransaction(
        { dispatch, commit },
        vehicle_transactionId
    ) {
        try {
            const res = await axios.delete(
                `/api/vehicle_transactions/${vehicle_transactionId}`
            );

            // commit(DELETE_VEHICLE_TRANSACTION, vehicle_transactionId);

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

    // Delete multiple vehicle_transactions
    async deleteMultipleVehicleTransactions({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(
                `/api/vehicle_transactions/delete_multiple`,
                {
                    headers: {},
                    data: { ids },
                }
            );

            // commit(DELETE_VEHICLE_TRANSACTIONS, ids);

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
    GET_VEHICLE_TRANSACTIONS: (state, payload) =>
        (state.vehicle_transactions = payload),

    GET_VEHICLE_TRANSACTION: (state, payload) =>
        (state.vehicle_transaction = payload),

    UPDATE_VEHICLE_TRANSACTION: (state, payload) => {
        const index = state.vehicle_transactions.findIndex(
            (vehicle_transaction) => vehicle_transaction.id === payload.id
        );
        state.vehicle_transactions.splice(index, 1, payload);
    },

    DELETE_VEHICLE_TRANSACTION: (state, payload) => {
        const index = state.vehicle_transactions.findIndex(
            (vehicle_transaction) => vehicle_transaction.id === payload
        );
        state.vehicle_transactions.splice(index, 1);
    },

    DELETE_VEHICLE_TRANSACTIONS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.vehicle_transactions.findIndex(
                (vehicle_transaction) => vehicle_transaction.id === id
            );
            state.vehicle_transactions.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
