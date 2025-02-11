import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_REPORT_DATA,
} from "../../mutation_constants";

const state = {
    reportData: [],
};

const getters = {
    reportData: (state) => state.reportData,
};

const actions = {
    // Get purchase report data
    async getPurchaseReportData({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/reports/purchase", data);

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });

            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
        }
    },

    // Get purchased items report data
    async getPurchasedItemsReportData({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/reports/purchased_items", data);

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });

            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
        }
    },

    // Get sell report data
    async getSellReportData({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/reports/sell", data);

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });

            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
        }
    },

    // Get sold items report data
    async getSoldItemsReportData({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/reports/sold_items", data);

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });

            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
        }
    },

    // Get receivables report
    async getReceivablesReportData(
        { dispatch, commit },
        { from_date, to_date }
    ) {
        try {
            const res = await axios.get(
                `/api/reports/receivables?from_date=${from_date}&to_date=${to_date}`
            );

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
        }
    },

    // Get payables report
    async getPayablesReportData({ dispatch, commit }, { from_date, to_date }) {
        try {
            const res = await axios.get(
                `/api/reports/payables?from_date=${from_date}&to_date=${to_date}`
            );

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
        }
    },

    // Get expense report data
    async getExpenseReportData({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/reports/expense", data);

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });

            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
        }
    },
    // Get machine report data
    async getMachineReportData({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/reports/machine", data);

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });

            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
        }
    },

    // Get salary report data
    async getSalaryReportData({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/reports/salary", data);

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });

            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
        }
    },

    // Get closing report data
    async getClosingReportData({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/reports/closing", data);

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });

            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
        }
    },

    // Get stock report data
    async getStockReportData({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/reports/stock", data);

            commit(GET_REPORT_DATA, res.data);
            commit(SET_LOADING, false, { root: true });
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });

            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
        }
    },
};

const mutations = {
    GET_REPORT_DATA: (state, payload) => (state.reportData = payload),
};

export default {
    state,
    getters,
    actions,
    mutations,
};
