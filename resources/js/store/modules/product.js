import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_PRODUCT,
    GET_PRODUCTS,
    GET_PRODUCT,
    UPDATE_PRODUCT,
    DELETE_PRODUCT,
    DELETE_PRODUCTS,
} from "../../mutation_constants";

const state = {
    products: [],
    product: null,
};

const getters = {
    products: (state) => state.products,
    product: (state) => state.product,
};

const actions = {
    // Add product
    async addProduct({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/products", data);

            commit(NEW_PRODUCT, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Product added successfully",
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

    // Get products
    async getProducts({ commit }) {
        try {
            const res = await axios.get("/api/products");

            commit(SET_LOADING, false, { root: true });
            commit(GET_PRODUCTS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single product
    async getProduct({ commit }, productId) {
        try {
            const res = await axios.get(`/api/products/${productId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PRODUCT, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update product
    async updateProduct({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/products/${data.id}`, data);

            commit(UPDATE_PRODUCT, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Product updated successfully",
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

    // Delete product
    async deleteProduct({ dispatch, commit }, productId) {
        try {
            const res = await axios.delete(`/api/products/${productId}`);

            commit(DELETE_PRODUCT, productId);

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

    // Delete multiple products
    async deleteMultipleProducts({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/products/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_PRODUCTS, ids);

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
    GET_PRODUCTS: (state, payload) => (state.products = payload),

    GET_PRODUCT: (state, payload) => (state.product = payload),

    NEW_PRODUCT: (state, payload) => {
        state.products.unshift(payload);
    },

    UPDATE_PRODUCT: (state, payload) => {
        const index = state.products.findIndex(
            (product) => product.id === payload.id
        );
        state.products.splice(index, 1, payload);
    },

    DELETE_PRODUCT: (state, payload) => {
        const index = state.products.findIndex(
            (product) => product.id === payload
        );
        state.products.splice(index, 1);
    },

    DELETE_PRODUCTS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.products.findIndex(
                (product) => product.id === id
            );
            state.products.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
