import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_CUSTOMER,
    GET_CUSTOMERS,
    GET_CUSTOMER,
    UPDATE_CUSTOMER,
    DELETE_CUSTOMER,
    DELETE_CUSTOMERS,
} from "../../mutation_constants";

const state = {
    customers: [],
    customer: null,
};

const getters = {
    customers: (state) => state.customers,
    customer: (state) => state.customer,
};

const actions = {
    // Add customer
    async addCustomer(
        { dispatch, commit },
        { name, cnic, phone, address, photo }
    ) {
        try {
            const fd = new FormData();

            fd.append("name", name);
            fd.append("cnic", cnic);
            fd.append("phone", phone);
            fd.append("address", address);
            fd.append("photo", photo);

            const res = await axios.post("/api/customers", fd);

            commit(NEW_CUSTOMER, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Customer added successfully",
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

    // Get customers
    async getCustomers({ commit }) {
        try {
            const res = await axios.get("/api/customers");

            commit(SET_LOADING, false, { root: true });
            commit(GET_CUSTOMERS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single customer
    async getCustomer({ commit }, customerId) {
        try {
            const res = await axios.get(`/api/customers/${customerId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_CUSTOMER, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update customer
    async updateCustomer(
        { dispatch, commit },
        { id, name, cnic, phone, address, photo }
    ) {
        try {
            const fd = new FormData();

            fd.append("name", name);
            fd.append("cnic", cnic);
            fd.append("phone", phone);
            fd.append("address", address);
            fd.append("photo", photo);
            fd.append("_method", "PUT");

            const res = await axios.post(`/api/customers/${id}`, fd);

            commit(UPDATE_CUSTOMER, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Customer updated successfully",
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

    // Delete customer
    async deleteCustomer({ dispatch, commit }, customerId) {
        try {
            const res = await axios.delete(`/api/customers/${customerId}`);

            commit(DELETE_CUSTOMER, customerId);

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

    // Delete multiple customers
    async deleteMultipleCustomers({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/customers/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_CUSTOMERS, ids);

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
    GET_CUSTOMERS: (state, payload) => (state.customers = payload),

    GET_CUSTOMER: (state, payload) => (state.customer = payload),

    NEW_CUSTOMER: (state, payload) => {
        state.customers.unshift(payload);
    },

    UPDATE_CUSTOMER: (state, payload) => {
        const index = state.customers.findIndex(
            (customer) => customer.id === payload.id
        );
        state.customers.splice(index, 1, payload);
    },

    DELETE_CUSTOMER: (state, payload) => {
        const index = state.customers.findIndex(
            (customer) => customer.id === payload
        );
        state.customers.splice(index, 1);
    },

    DELETE_CUSTOMERS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.customers.findIndex(
                (customer) => customer.id === id
            );
            state.customers.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
