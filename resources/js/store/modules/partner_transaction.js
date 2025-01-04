import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_PARTNER_TRANSACTIONS,
    UPDATE_PARTNER_TRANSACTION,
    DELETE_PARTNER_TRANSACTION,
    DELETE_PARTNER_TRANSACTIONS,
    GET_PARTNER_TRANSACTION,
    NEW_PARTNER_TRANSACTION,
    OLD_PARTNER_TRANSACTION,
} from "../../mutation_constants";

const state = {
    partner_transactions: [],
    partner_transaction: null,
    recent_partner_transaction: null,
    old_partner_transaction: null,
    loading: false,
    totals: null,
};

const getters = {
    partner_transactions: (state) => state.partner_transactions,
    partner_transaction: (state) => state.partner_transaction,
    recent_partner_transaction: (state) => state.recent_partner_transaction,
    old_partner_transaction: (state) => state.old_partner_transaction,
    loading: (state) => state.loading,
    totals: (state) => state.totals,
};

const actions = {
    // Add partner transaction
    async addPartnerTransaction({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/partner_transactions", data);

            commit(NEW_PARTNER_TRANSACTION, res.data);
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

    // Get partner transactions
    async getPartnerTransactions(
        { commit },
        { partnerId, search, from_date, to_date }
    ) {
        try {
            commit(SET_LOADING, true);

            const res = await axios.get(
                `/api/partner_transactions?partner_id=${partnerId}&search=${search}&from_date=${from_date}&to_date=${to_date}`
            );
            commit(SET_LOADING, false);
            commit(GET_PARTNER_TRANSACTIONS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Get a single partner transaction
    async getPartnerTransaction({ commit }, id) {
        try {
            const res = await axios.get(`/api/partner_transactions/${id}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PARTNER_TRANSACTION, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update partner_transaction
    async updatePartnerTransaction({ commit }, data) {
        try {
            const res = await axios.put(
                `/api/partner_transactions/${data.id}`,
                data
            );

            commit(
                UPDATE_PARTNER_TRANSACTION,
                res.data.updated_partner_transaction
            );
            commit(OLD_PARTNER_TRANSACTION, res.data.old_partner_transaction);
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

    // Delete partner_transaction
    async deletePartnerTransaction(
        { dispatch, commit },
        partner_transactionId
    ) {
        try {
            const res = await axios.delete(
                `/api/partner_transactions/${partner_transactionId}`
            );

            commit(DELETE_PARTNER_TRANSACTION, partner_transactionId);

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

    GET_PARTNER_TRANSACTIONS: (state, payload) => {
        state.partner_transactions = payload.data;
        state.totals = payload.totals;
        state.total = payload.total;
    },

    GET_PARTNER_TRANSACTION: (state, payload) =>
        (state.partner_transaction = payload),

    NEW_PARTNER_TRANSACTION: (state, payload) => {
        state.recent_partner_transaction = payload;
    },

    OLD_PARTNER_TRANSACTION: (state, payload) => {
        state.old_partner_transaction = payload;
    },

    UPDATE_PARTNER_TRANSACTION: (state, payload) => {
        const index = state.partner_transactions.findIndex(
            (partner_transaction) => partner_transaction.id === payload.id
        );
        state.partner_transactions.splice(index, 1, payload);
    },

    DELETE_PARTNER_TRANSACTION: (state, payload) => {
        const index = state.partner_transactions.findIndex(
            (partner_transaction) => partner_transaction.id === payload
        );
        state.partner_transactions.splice(index, 1);
    },

    DELETE_PARTNER_TRANSACTIONS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.partner_transactions.findIndex(
                (partner_transaction) => partner_transaction.id === id
            );
            state.partner_transactions.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
