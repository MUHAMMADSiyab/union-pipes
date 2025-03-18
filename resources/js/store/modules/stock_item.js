import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_STOCK_ITEM,
    GET_STOCK_ITEMS,
    GET_STOCK_ITEM,
    UPDATE_STOCK_ITEM,
    DELETE_STOCK_ITEM,
    DELETE_STOCK_ITEMS,
} from "../../mutation_constants";

const state = {
    stock_items: [],
    stock_item: null,
};

const getters = {
    stock_items: (state) => state.stock_items,
    stock_item: (state) => state.stock_item,
};

const actions = {
    // Add stock item
    async addStockItem({ dispatch, commit }, data) {
        try {
            data.product_id = data.product_id?.id;
            const res = await axios.post("/api/stock_items", data);

            commit(NEW_STOCK_ITEM, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Stock Item added successfully",
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

    // Get stock items
    async getStockItems({ commit }) {
        try {
            const res = await axios.get("/api/stock_items");

            commit(SET_LOADING, false, { root: true });
            commit(GET_STOCK_ITEMS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single stock item
    async getStockItem({ commit }, stockItemId) {
        try {
            const res = await axios.get(`/api/stock_items/${stockItemId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_STOCK_ITEM, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update stock item
    async updateStockItem({ dispatch, commit }, data) {
        try {
            data.product_id = data.product_id?.id;
            const res = await axios.put(`/api/stock_items/${data.id}`, data);

            commit(UPDATE_STOCK_ITEM, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Stock Item updated successfully",
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

    // Delete stock item
    async deleteStockItem({ dispatch, commit }, stockItemId) {
        try {
            const res = await axios.delete(`/api/stock_items/${stockItemId}`);

            commit(DELETE_STOCK_ITEM, stockItemId);

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

    // Delete multiple stock items
    async deleteMultipleStockItems({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/stock_items/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_STOCK_ITEMS, ids);

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
    GET_STOCK_ITEMS: (state, payload) => (state.stock_items = payload),

    GET_STOCK_ITEM: (state, payload) => (state.stock_item = payload),

    NEW_STOCK_ITEM: (state, payload) => {
        state.stock_items.unshift(payload);
    },

    UPDATE_STOCK_ITEM: (state, payload) => {
        const index = state.stock_items.findIndex(
            (stock_item) => stock_item.id === payload.id
        );
        state.stock_items.splice(index, 1, payload);
    },

    DELETE_STOCK_ITEM: (state, payload) => {
        const index = state.stock_items.findIndex(
            (stock_item) => stock_item.id === payload
        );
        state.stock_items.splice(index, 1);
    },

    DELETE_STOCK_ITEMS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.stock_items.findIndex(
                (stock_item) => stock_item.id === id
            );
            state.stock_items.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
