import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_PURCHASE_ITEMS,
    UPDATE_PURCHASE_ITEM,
    DELETE_PURCHASE_ITEM,
    DELETE_PURCHASE_ITEMS,
    GET_PURCHASE_ITEM,
    NEW_PURCHASE_ITEM,
} from "../../mutation_constants";

const state = {
    purchase_items: [],
    purchase_item: null,
};

const getters = {
    purchase_items: (state) => state.purchase_items,
    purchase_item: (state) => state.purchase_item,
};

const actions = {
    // Add purchase item
    async addPurchaseItem({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/purchase_items", data);

            commit(NEW_PURCHASE_ITEM, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Purchase Item added successfully",
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

    // Get purchase items
    async getPurchaseItems({ commit }) {
        try {
            const res = await axios.get("/api/purchase_items");

            commit(SET_LOADING, false, { root: true });
            commit(GET_PURCHASE_ITEMS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single purchase item
    async getPurchaseItem({ commit }, purchaseItemId) {
        try {
            const res = await axios.get(`/api/purchase_items/${purchaseItemId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PURCHASE_ITEM, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update purchase item
    async updatePurchaseItem({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/purchase_items/${data.id}`, data);

            commit(UPDATE_PURCHASE_ITEM, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "PurchaseItem updated successfully",
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

    // Delete purchase item
    async deletePurchaseItem({ dispatch, commit }, purchaseItemId) {
        try {
            const res = await axios.delete(`/api/purchase_items/${purchaseItemId}`);

            commit(DELETE_PURCHASE_ITEM, purchaseItemId);

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

    // Delete multiple purchase items
    async deleteMultiplePurchaseItems({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/purchase_items/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_PURCHASE_ITEMS, ids);

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
    GET_PURCHASE_ITEMS: (state, payload) => (state.purchase_items = payload),

    GET_PURCHASE_ITEM: (state, payload) => (state.purchase_item = payload),

    NEW_PURCHASE_ITEM: (state, payload) => {
        state.purchase_items.unshift(payload);
    },

    UPDATE_PURCHASE_ITEM: (state, payload) => {
        const index = state.purchase_items.findIndex((purchase_item) => purchase_item.id === payload.id);
        state.purchase_items.splice(index, 1, payload);
    },

    DELETE_PURCHASE_ITEM: (state, payload) => {
        const index = state.purchase_items.findIndex((purchase_item) => purchase_item.id === payload);
        state.purchase_items.splice(index, 1);
    },

    DELETE_PURCHASE_ITEMS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.purchase_items.findIndex((purchase_item) => purchase_item.id === id);
            state.purchase_items.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
