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

const state = {
    purchases: [],
    purchase: null,
    recent_purchase: null,
    old_purchase: null,
};

const getters = {
    purchases: (state) => state.purchases,
    purchase: (state) => state.purchase,
    recent_purchase: (state) => state.recent_purchase,
    old_purchase: (state) => state.old_purchase,
};

const actions = {
    // Add purchase
    async addPurchase({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/purchases", data);

            commit(NEW_PURCHASE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Purchase added successfully",
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
    async getPurchases({ commit }) {
        try {
            const res = await axios.get("/api/purchases");

            commit(SET_LOADING, false, { root: true });
            commit(GET_PURCHASES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
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
            const res = await axios.put(`/api/purchases/${data.id}`, data);

            commit(UPDATE_PURCHASE, res.data.updated_purchase);
            commit(OLD_PURCHASE, res.data.old_purchase);
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
    GET_PURCHASES: (state, payload) => (state.purchases = payload),

    GET_PURCHASE: (state, payload) => (state.purchase = payload),

    NEW_PURCHASE: (state, payload) => {
        state.recent_purchase = payload;
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
