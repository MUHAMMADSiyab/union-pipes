import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_INVOICE,
    GET_INVOICES,
    GET_INVOICE,
    UPDATE_INVOICE,
    DELETE_INVOICE,
    DELETE_INVOICES,
} from "../../mutation_constants";

const state = {
    invoices: [],
    invoice: null,
};

const getters = {
    invoices: (state) => state.invoices,
    invoice: (state) => state.invoice,
};

const actions = {
    // Add invoice
    async addInvoice({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/invoices", data);

            commit(NEW_INVOICE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Invoice added successfully",
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

    // Get invoices
    async getInvoices({ commit }) {
        try {
            const res = await axios.get("/api/invoices");

            commit(SET_LOADING, false, { root: true });
            commit(GET_INVOICES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single invoice
    async getInvoice({ commit }, invoiceId) {
        try {
            const res = await axios.get(`/api/invoices/${invoiceId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_INVOICE, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update invoice
    async updateInvoice({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/invoices/${data.id}`, data);

            commit(UPDATE_INVOICE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Invoice updated successfully",
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

    // Delete invoice
    async deleteInvoice({ dispatch, commit }, invoiceId) {
        try {
            const res = await axios.delete(`/api/invoices/${invoiceId}`);

            commit(DELETE_INVOICE, invoiceId);

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

    // Delete multiple invoices
    async deleteMultipleInvoices({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/invoices/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_INVOICES, ids);

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
    GET_INVOICES: (state, payload) => (state.invoices = payload),

    GET_INVOICE: (state, payload) => (state.invoice = payload),

    NEW_INVOICE: (state, payload) => {
        state.invoices.unshift(payload);
    },

    UPDATE_INVOICE: (state, payload) => {
        const index = state.invoices.findIndex(
            (invoice) => invoice.id === payload.id
        );
        state.invoices.splice(index, 1, payload);
    },

    DELETE_INVOICE: (state, payload) => {
        const index = state.invoices.findIndex(
            (invoice) => invoice.id === payload
        );
        state.invoices.splice(index, 1);
    },

    DELETE_INVOICES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.invoices.findIndex(
                (invoice) => invoice.id === id
            );
            state.invoices.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
