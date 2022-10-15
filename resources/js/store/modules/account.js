import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_ACCOUNTS,
    UPDATE_ACCOUNT,
    DELETE_ACCOUNT,
    DELETE_ACCOUNTS,
    GET_ACCOUNT,
    NEW_ACCOUNT,
    OLD_ACCOUNT,
} from "../../mutation_constants";

const state = {
    accounts: [],
    account: null,
    recent_account: null,
    old_account: null,
};

const getters = {
    accounts: (state) => state.accounts,
    account: (state) => state.account,
    recent_account: (state) => state.recent_account,
    old_account: (state) => state.old_account,
};

const actions = {
    // Add account
    async addAccount({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/accounts", data);

            commit(NEW_ACCOUNT, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            if (!data.receive_payment) {
                return dispatch(
                    "alert/setAlert",
                    {
                        type: "success",
                        message: "Account entry added successfully",
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

    // Get accounts
    async getAccounts({ commit }, customerId) {
        try {
            const res = await axios.get(
                `/api/accounts/get_customer_accounts/${customerId}`
            );

            commit(SET_LOADING, false, { root: true });
            commit(GET_ACCOUNTS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single account
    async getAccount({ commit }, accountId) {
        try {
            const res = await axios.get(`/api/accounts/${accountId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_ACCOUNT, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update account
    async updateAccount({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/accounts/${data.id}`, data);

            commit(UPDATE_ACCOUNT, res.data.updated_account);
            commit(OLD_ACCOUNT, res.data.old_account);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            if (!data.receive_payment) {
                return dispatch(
                    "alert/setAlert",
                    {
                        type: "success",
                        message: "Account entry updated successfully",
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

    // Delete account
    async deleteAccount({ dispatch, commit }, accountId) {
        try {
            const res = await axios.delete(`/api/accounts/${accountId}`);

            // commit(DELETE_ACCOUNT, accountId);

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

    // Delete multiple accounts
    async deleteMultipleAccounts({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/accounts/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            // commit(DELETE_ACCOUNTS, ids);

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
    GET_ACCOUNTS: (state, payload) => (state.accounts = payload),

    GET_ACCOUNT: (state, payload) => (state.account = payload),

    NEW_ACCOUNT: (state, payload) => {
        state.recent_account = payload;
    },

    OLD_ACCOUNT: (state, payload) => {
        state.old_account = payload;
    },

    UPDATE_ACCOUNT: (state, payload) => {
        const index = state.accounts.findIndex(
            (account) => account.id === payload.id
        );
        state.accounts.splice(index, 1, payload);
    },

    DELETE_ACCOUNT: (state, payload) => {
        const index = state.accounts.findIndex(
            (account) => account.id === payload
        );
        state.accounts.splice(index, 1);
    },

    DELETE_ACCOUNTS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.accounts.findIndex(
                (account) => account.id === id
            );
            state.accounts.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
