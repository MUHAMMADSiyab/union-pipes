import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_STOCK_SHEETS,
    UPDATE_STOCK_SHEET,
    DELETE_STOCK_SHEET,
    DELETE_STOCK_SHEETS,
    GET_STOCK_SHEET,
    NEW_STOCK_SHEET,
} from "../../mutation_constants";

const state = {
    stock_sheets: [],
    stock_sheet: null,
    loading: false,
};

const getters = {
    stock_sheets: (state) => state.stock_sheets,
    stock_sheet: (state) => state.stock_sheet,
    loading: (state) => state.loading,
};

const actions = {
    // Add stock sheet
    async addStockSheet({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/stock_sheets", data);

            commit(NEW_STOCK_SHEET, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.message,
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

    // Get stock sheets
    async getStockSheets({ commit }) {
        try {
            const res = await axios.get("/api/stock_sheets");

            commit(SET_LOADING, false, { root: true });
            commit(GET_STOCK_SHEETS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single stock sheet
    async getStockSheet({ commit }, rawMaterialId) {
        try {
            const res = await axios.get(`/api/stock_sheets/${rawMaterialId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_STOCK_SHEET, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update stock sheet
    async updateStockSheet({ dispatch, commit }, data) {
        try {
            const res = await axios.put(`/api/stock_sheets/${data.id}`, data);

            commit(UPDATE_STOCK_SHEET, res.data.stock_sheet);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.message,
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

    // Delete stock sheet
    async deleteStockSheet({ dispatch, commit }, rawMaterialId) {
        try {
            const res = await axios.delete(
                `/api/stock_sheets/${rawMaterialId}`
            );

            commit(DELETE_STOCK_SHEET, rawMaterialId);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.message,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },

    // Delete multiple stock sheets
    async deleteMultipleStockSheets({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(
                `/api/stock_sheets/delete_multiple`,
                {
                    headers: {},
                    data: { ids },
                }
            );

            commit(DELETE_STOCK_SHEETS, ids);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.message,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },
};

const mutations = {
    GET_STOCK_SHEETS: (state, payload) => (state.stock_sheets = payload),

    GET_STOCK_SHEET: (state, payload) => (state.stock_sheet = payload),

    GET_LEDGER_ENTRIES: (state, payload) => (state.ledger_entries = payload),

    NEW_STOCK_SHEET: (state, payload) => {
        state.stock_sheets.unshift(payload);
    },

    UPDATE_STOCK_SHEET: (state, payload) => {
        const index = state.stock_sheets.findIndex(
            (stock_sheet) => stock_sheet.id === payload.id
        );
        state.stock_sheets.splice(index, 1, payload);
    },

    DELETE_STOCK_SHEET: (state, payload) => {
        const index = state.stock_sheets.findIndex(
            (stock_sheet) => stock_sheet.id === payload
        );
        state.stock_sheets.splice(index, 1);
    },

    DELETE_STOCK_SHEETS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.stock_sheets.findIndex(
                (stock_sheet) => stock_sheet.id === id
            );
            state.stock_sheets.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
