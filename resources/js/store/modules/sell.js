import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_SELLS,
    UPDATE_SELL,
    DELETE_SELL,
    DELETE_SELLS,
    PAYMENT,
    GET_SELL,
    NEW_SELL,
    OLD_SELL,
} from "../../mutation_constants";

const state = {
    sells: [],
    sell: null,
    final_readings: [],
    recent_sell: null,
    old_sell: null,
    loading: false,
    total: 0,
};

const getters = {
    sells: (state) => state.sells,
    sell: (state) => state.sell,
    final_readings: (state) => state.final_readings,
    recent_sell: (state) => state.recent_sell,
    old_sell: (state) => state.old_sell,
    total: (state) => state.total,
    loading: (state) => state.loading,
};

const actions = {
    // Add sell
    async addSell({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/sells", data);

            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Sell added successfully",
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

    // Get sells
    async getSells(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc, local }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "date";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/sells?local=${local}&page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_SELLS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Search sells
    async searchSells(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc, search, local }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "date";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/search_sells?local=${local}&search=${search}&page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_SELLS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Get a single sell
    async getSell({ commit }, id) {
        try {
            const res = await axios.get(`/api/sells/${id}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_SELL, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update sell
    async updateSell({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/sells/${data.id}`, data);

            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Sell updated successfully",
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
            console.log(error);
        }
    },

    // Delete sell
    async deleteSell({ dispatch, commit }, sellId) {
        try {
            const res = await axios.delete(`/api/sells/${sellId}`);

            commit(DELETE_SELL, sellId);

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

    // Delete multiple sells
    async deleteMultipleSells({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/sells/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_SELLS, ids);

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

    // Return sold items
    async returnSoldItems({ dispatch, commit }, data) {
        try {
            const res = await axios.post(`/api/returned_sold_items`, data);

            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
            commit(PAYMENT, res.data);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Items returned successfully",
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
            console.log(error);
        }
    },
};

const mutations = {
    SET_LOADING: (state, payload) => (state.loading = payload),

    GET_SELLS: (state, payload) => {
        state.sells = payload.data;
        state.total = payload.total;
    },

    GET_SELL: (state, payload) => (state.sell = payload),

    NEW_SELL: (state, payload) => {
        state.recent_sell = payload;
    },

    PAYMENT: (state, payload) => {
        const index = state.sells.findIndex((sell) => sell.id === payload.id);
        state.sells.splice(index, 1, payload);
    },

    OLD_SELL: (state, payload) => {
        state.old_sell = payload;
    },

    UPDATE_SELL: (state, payload) => {
        const index = state.sells.findIndex((sell) => sell.id === payload.id);
        state.sells.splice(index, 1, payload);
    },

    DELETE_SELL: (state, payload) => {
        const index = state.sells.findIndex((sell) => sell.id === payload);
        state.sells.splice(index, 1);
    },

    DELETE_SELLS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.sells.findIndex((sell) => sell.id === id);
            state.sells.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
