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
