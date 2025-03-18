import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_PURCHASES,
    UPDATE_PURCHASE,
    DELETE_PURCHASE,
    DELETE_PURCHASES,
    GET_PURCHASE,
    NEW_PURCHASE,
    OLD_PURCHASE,
} from "../../mutation_constants";

import moment from "moment";

const state = {
    purchases: [],
    purchase: null,
    recent_purchase: null,
    old_purchase: null,
    total: 0,
    loading: false,
};

const getters = {
    purchases: (state) => state.purchases,
    total: (state) => state.total,
    loading: (state) => state.loading,
    purchase: (state) => state.purchase,
    recent_purchase: (state) => state.recent_purchase,
    old_purchase: (state) => state.old_purchase,
};

const actions = {
    // Add purchase
    async addPurchase({ commit, dispatch }, data) {
        try {
            data.date = moment(data.date).format("Y-MM-DD HH:mm:ss");
            data.company_id = data.company_id?.id;
            data.items = data.items.map((item) => ({
                ...item,
                purchase_item_id: item.purchase_item_id?.id,
            }));
            const res = await axios.post("/api/purchases", data);

            commit(NEW_PURCHASE, res.data);
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

    // Get purchases
    async getPurchases({ commit }, { page, itemsPerPage, sortBy, sortDesc }) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "date";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/purchases?page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_PURCHASES, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Search purchases
    async searchPurchases(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc, search }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "date";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/search_purchases?search=${search}&page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_PURCHASES, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Get a single purchase
    async getPurchase({ commit }, id) {
        try {
            const res = await axios.get(`/api/purchases/${id}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PURCHASE, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update purchase
    async updatePurchase({ dispatch, commit }, data) {
        try {
            data.date = moment(data.date).format("Y-MM-DD HH:mm:ss");
            data.company_id = data.company_id?.id;
            data.items = data.items.map((item) => ({
                ...item,
                purchase_item_id: item.purchase_item_id?.id,
            }));
            const res = await axios.put(`/api/purchases/${data.id}`, data);

            // commit(UPDATE_PURCHASE, res.data.updated_purchase);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Purchase updated successfully",
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

    // Delete purchase
    async deletePurchase({ dispatch, commit }, purchaseId) {
        try {
            const res = await axios.delete(`/api/purchases/${purchaseId}`);

            commit(DELETE_PURCHASE, purchaseId);

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

    // Delete multiple purchases
    async deleteMultiplePurchases({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/purchases/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_PURCHASES, ids);

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

    GET_PURCHASES: (state, payload) => {
        state.purchases = payload.data;
        state.total = payload.total;
    },

    GET_PURCHASE: (state, payload) => (state.purchase = payload),

    NEW_PURCHASE: (state, payload) => {
        state.recent_purchase = payload;
    },

    PAYMENT: (state, payload) => {
        const index = state.purchases.findIndex(
            (purchase) => purchase.id === payload.id
        );
        state.purchases.splice(index, 1, payload);
    },

    OLD_PURCHASE: (state, payload) => {
        state.old_purchase = payload;
    },

    UPDATE_PURCHASE: (state, payload) => {
        const index = state.purchases.findIndex(
            (purchase) => purchase.id === payload.id
        );
        state.purchases.splice(index, 1, payload);
    },

    DELETE_PURCHASE: (state, payload) => {
        const index = state.purchases.findIndex(
            (purchase) => purchase.id === payload
        );
        state.purchases.splice(index, 1);
    },

    DELETE_PURCHASES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.purchases.findIndex(
                (purchase) => purchase.id === id
            );
            state.purchases.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
