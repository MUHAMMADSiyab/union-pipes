import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_TRANSACTIONS,
    UPDATE_TRANSACTION,
    DELETE_TRANSACTION,
    DELETE_TRANSACTIONS,
    GET_TRANSACTION,
    NEW_TRANSACTION,
    OLD_TRANSACTION,
} from "../../mutation_constants";

const state = {
    transactions: [],
    transaction: null,
    recent_transaction: null,
    old_transaction: null,
    loading: false,
    total: 0,
};

const getters = {
    transactions: (state) => state.transactions,
    transaction: (state) => state.transaction,
    recent_transaction: (state) => state.recent_transaction,
    old_transaction: (state) => state.old_transaction,
    loading: (state) => state.loading,
    total: (state) => state.total,
};

const actions = {
    // Add transaction
    async addTransaction({ commit }, data) {
        try {
            const res = await axios.post("/api/transactions", data);

            commit(NEW_TRANSACTION, res.data);
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

    // Get transactions
    async getTransactions(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "id";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/transactions?page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_TRANSACTIONS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Search transactions
    async searchTransactions(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc, search }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "id";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/search_transactions?search=${search}&page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_TRANSACTIONS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Get a single transaction
    async getTransaction({ commit }, vehicleAdjustmentId) {
        try {
            const res = await axios.get(
                `/api/transactions/${vehicleAdjustmentId}`
            );

            commit(SET_LOADING, false, { root: true });
            commit(GET_TRANSACTION, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update transaction
    async updateTransaction({ commit }, data) {
        try {
            const res = await axios.put(`/api/transactions/${data.id}`, data);

            commit(UPDATE_TRANSACTION, res.data.updated_transaction);
            commit(OLD_TRANSACTION, res.data.old_transaction);
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

    // Delete transaction
    async deleteTransaction({ dispatch, commit }, transactionId) {
        try {
            const res = await axios.delete(
                `/api/transactions/${transactionId}`
            );

            commit(DELETE_TRANSACTION, transactionId);

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

    // Delete multiple transactions
    async deleteMultipleTransactions({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(
                `/api/transactions/delete_multiple`,
                {
                    headers: {},
                    data: { ids },
                }
            );

            commit(DELETE_TRANSACTIONS, ids);

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

    GET_TRANSACTIONS: (state, payload) => {
        state.transactions = payload.data;
        state.total = payload.total;
    },

    GET_TRANSACTION: (state, payload) => (state.transaction = payload),

    NEW_TRANSACTION: (state, payload) => {
        state.recent_transaction = payload;
    },

    OLD_TRANSACTION: (state, payload) => {
        state.old_transaction = payload;
    },

    UPDATE_TRANSACTION: (state, payload) => {
        const index = state.transactions.findIndex(
            (transaction) => transaction.id === payload.id
        );
        state.transactions.splice(index, 1, payload);
    },

    DELETE_TRANSACTION: (state, payload) => {
        const index = state.transactions.findIndex(
            (transaction) => transaction.id === payload
        );
        state.transactions.splice(index, 1);
    },

    DELETE_TRANSACTIONS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.transactions.findIndex(
                (transaction) => transaction.id === id
            );
            state.transactions.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
