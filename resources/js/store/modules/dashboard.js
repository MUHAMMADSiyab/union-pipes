import axios from "axios";
import {
    SET_LOADING,
    GET_DASHBOARD_DATA,
    UPDATE_UNCLEARED_CHEQUES,
    UPDATE_ALL_UNCLEARED_CHEQUES,
} from "../../mutation_constants";

const state = {
    dashboardData: null,
};

const getters = {
    dashboardData: (state) => state.dashboardData,
};

const actions = {
    // Get counts
    async getDashboardData({ commit }) {
        try {
            const res = await axios.post(`/api/dashboard`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_DASHBOARD_DATA, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    async markChequesAsCleared({ commit }, paymentId) {
        try {
            const res = await axios.put(`/api/uncleared_cheques/${paymentId}`);

            commit(UPDATE_UNCLEARED_CHEQUES, res.data.id);
        } catch (error) {}
    },

    async markAllChequesAsCleared({ commit }) {
        try {
            await axios.put(`/api/uncleared_cheques`);

            commit(UPDATE_ALL_UNCLEARED_CHEQUES);
        } catch (error) {}
    },
};

const mutations = {
    GET_DASHBOARD_DATA: (state, payload) => (state.dashboardData = payload),

    UPDATE_UNCLEARED_CHEQUES: (state, payload) => {
        state.dashboardData.unclearedCheques =
            state.dashboardData.unclearedCheques.filter(
                (item) => item.id != payload
            );
    },

    UPDATE_ALL_UNCLEARED_CHEQUES: (state) => {
        state.dashboardData.unclearedCheques = [];
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
