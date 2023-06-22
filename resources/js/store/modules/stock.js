import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_PRODUCTION,
    GET_PRODUCTIONS,
    GET_PRODUCTION,
    UPDATE_PRODUCTION,
    DELETE_PRODUCTION,
    DELETE_PRODUCTIONS,
} from "../../mutation_constants";

const state = {
    stocks: [],
    stock: null,
};

const getters = {
    stocks: (state) => state.stocks,
    stock: (state) => state.stock,
};

const actions = {
    // Add stock
    async addStock({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/stocks", data);

            commit(NEW_PRODUCTION, res.data.stock);
            commit("stock_item/UPDATE_STOCK_ITEM", res.data.stock_item, {
                root: true,
            });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Stock added successfully",
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

    // Get stocks
    async getStocks({ commit }, stockItemId) {
        try {
            const res = await axios.get(
                `/api/stocks/${stockItemId}/get_stock_item_stocks`
            );

            commit(SET_LOADING, false, { root: true });
            commit(GET_PRODUCTIONS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single stock
    async getStock({ commit }, stockId) {
        try {
            const res = await axios.get(`/api/stocks/${stockId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PRODUCTION, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update stock
    async updateStock({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/stocks/${data.id}`, data);

            commit(UPDATE_PRODUCTION, res.data.stock);
            commit("stock_item/UPDATE_STOCK_ITEM", res.data.stock_item, {
                root: true,
            });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Stock updated successfully",
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

    // Delete stock
    async deleteStock({ dispatch, commit }, stockId) {
        try {
            const res = await axios.delete(`/api/stocks/${stockId}`);

            commit(DELETE_PRODUCTION, stockId);
            commit("stock_item/UPDATE_STOCK_ITEM", res.data, {
                root: true,
            });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Stock deleted successfully",
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },

    // Delete multiple stocks
    async deleteMultipleStocks({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/stocks/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_PRODUCTIONS, ids);

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
    GET_PRODUCTIONS: (state, payload) => (state.stocks = payload),

    GET_PRODUCTION: (state, payload) => (state.stock = payload),

    NEW_PRODUCTION: (state, payload) => {
        state.stocks.unshift(payload);
    },

    UPDATE_PRODUCTION: (state, payload) => {
        const index = state.stocks.findIndex(
            (stock) => stock.id === payload.id
        );
        state.stocks.splice(index, 1, payload);
    },

    DELETE_PRODUCTION: (state, payload) => {
        const index = state.stocks.findIndex((stock) => stock.id === payload);
        state.stocks.splice(index, 1);
    },

    DELETE_PRODUCTIONS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.stocks.findIndex((stock) => stock.id === id);
            state.stocks.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
