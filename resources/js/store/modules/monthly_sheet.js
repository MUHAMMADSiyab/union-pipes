import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_MONTHLY_SHEETS,
    UPDATE_MONTHLY_SHEET,
    DELETE_MONTHLY_SHEET,
    DELETE_MONTHLY_SHEETS,
    GET_MONTHLY_SHEET,
    NEW_MONTHLY_SHEET,
} from "../../mutation_constants";

const state = {
    monthly_sheets: [],
    monthly_sheet: null,
    loading: false,
};

const getters = {
    monthly_sheets: (state) => state.monthly_sheets,
    monthly_sheet: (state) => state.monthly_sheet,
    loading: (state) => state.loading,
};

const actions = {
    // Add monthly sheet
    async addMonthlySheet({ dispatch, commit }, data) {
        try {
            const assets = data.assets.map((asset) => ({
                ...asset,
                category: "asset",
            }));
            const payables = data.payables.map((payable) => ({
                ...payable,
                category: "payable",
            }));
            const payload = {
                month: data.month,
                previous_month_total: data.previousMonthTotal,
                entries: [...assets, ...payables],
            };

            const res = await axios.post("/api/monthly_sheets", payload);

            commit(NEW_MONTHLY_SHEET, res.data);
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

    // Get monthly sheets
    async getMonthlySheets({ commit }) {
        try {
            const res = await axios.get("/api/monthly_sheets");

            commit(SET_LOADING, false, { root: true });
            commit(GET_MONTHLY_SHEETS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single monthly sheet
    async getMonthlySheet({ commit }, rawMaterialId) {
        try {
            const res = await axios.get(`/api/monthly_sheets/${rawMaterialId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_MONTHLY_SHEET, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update monthly sheet
    async updateMonthlySheet({ dispatch, commit }, data) {
        try {
            const assets = data.assets.map((asset) => ({
                ...asset,
                category: "asset",
            }));
            const payables = data.payables.map((payable) => ({
                ...payable,
                category: "payable",
            }));
            const payload = {
                month: data.month,
                previous_month_total: data.previousMonthTotal,
                entries: [...assets, ...payables],
            };

            const res = await axios.put(
                `/api/monthly_sheets/${data.id}`,
                payload
            );

            commit(UPDATE_MONTHLY_SHEET, res.data.monthly_sheet);
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

    // Delete monthly sheet
    async deleteMonthlySheet({ dispatch, commit }, rawMaterialId) {
        try {
            const res = await axios.delete(
                `/api/monthly_sheets/${rawMaterialId}`
            );

            commit(DELETE_MONTHLY_SHEET, rawMaterialId);

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

    // Delete multiple monthly sheets
    async deleteMultipleMonthlySheets({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(
                `/api/monthly_sheets/delete_multiple`,
                {
                    headers: {},
                    data: { ids },
                }
            );

            commit(DELETE_MONTHLY_SHEETS, ids);

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
    GET_MONTHLY_SHEETS: (state, payload) => (state.monthly_sheets = payload),

    GET_MONTHLY_SHEET: (state, payload) => (state.monthly_sheet = payload),

    GET_LEDGER_ENTRIES: (state, payload) => (state.ledger_entries = payload),

    NEW_MONTHLY_SHEET: (state, payload) => {
        state.monthly_sheets.unshift(payload);
    },

    UPDATE_MONTHLY_SHEET: (state, payload) => {
        const index = state.monthly_sheets.findIndex(
            (monthly_sheet) => monthly_sheet.id === payload.id
        );
        state.monthly_sheets.splice(index, 1, payload);
    },

    DELETE_MONTHLY_SHEET: (state, payload) => {
        const index = state.monthly_sheets.findIndex(
            (monthly_sheet) => monthly_sheet.id === payload
        );
        state.monthly_sheets.splice(index, 1);
    },

    DELETE_MONTHLY_SHEETS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.monthly_sheets.findIndex(
                (monthly_sheet) => monthly_sheet.id === id
            );
            state.monthly_sheets.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
