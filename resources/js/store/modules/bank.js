import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_BANKS,
    UPDATE_BANK,
    DELETE_BANK,
    DELETE_BANKS,
    GET_BANK,
    NEW_BANK,
    GET_LEDGER_ENTRIES,
} from "../../mutation_constants";

const state = {
    banks: [],
    bank: null,
    ledger_entries: [],
};

const getters = {
    banks: (state) => state.banks,
    bank: (state) => state.bank,
    ledger_entries: (state) => state.ledger_entries,
};

const actions = {
    // Add bank
    async addBank({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/banks", data);

            commit(NEW_BANK, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Bank added successfully",
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

    // Get banks
    async getBanks({ commit }) {
        try {
            const res = await axios.get("/api/banks");

            commit(SET_LOADING, false, { root: true });
            commit(GET_BANKS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single bank
    async getBank({ commit }, bankId) {
        try {
            const res = await axios.get(`/api/banks/${bankId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_BANK, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get bank's ledger entries data
    async getLedgerEntries({ commit }, bankId) {
        try {
            const res = await axios.post(`/api/banks/${bankId}/ledger_entries`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_LEDGER_ENTRIES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update bank
    async updateBank({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/banks/${data.id}`, data);

            commit(UPDATE_BANK, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Bank updated successfully",
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

    // Delete bank
    async deleteBank({ dispatch, commit }, bankId) {
        try {
            const res = await axios.delete(`/api/banks/${bankId}`);

            commit(DELETE_BANK, bankId);

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

    // Delete multiple banks
    async deleteMultipleBanks({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/banks/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_BANKS, ids);

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
    GET_BANKS: (state, payload) => (state.banks = payload),

    GET_BANK: (state, payload) => (state.bank = payload),

    GET_LEDGER_ENTRIES: (state, payload) => (state.ledger_entries = payload),

    NEW_BANK: (state, payload) => {
        state.banks.unshift(payload);
    },

    UPDATE_BANK: (state, payload) => {
        const index = state.banks.findIndex((bank) => bank.id === payload.id);
        state.banks.splice(index, 1, payload);
    },

    DELETE_BANK: (state, payload) => {
        const index = state.banks.findIndex((bank) => bank.id === payload);
        state.banks.splice(index, 1);
    },

    DELETE_BANKS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.banks.findIndex((bank) => bank.id === id);
            state.banks.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
