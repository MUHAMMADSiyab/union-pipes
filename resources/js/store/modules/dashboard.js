import axios from "axios";
import { SET_LOADING, GET_DASHBOARD_DATA } from "../../mutation_constants";

const state = {
    dashboardData: []
};

const getters = {
    dashboardData: state => state.dashboardData
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
    }
};

const mutations = {
    GET_DASHBOARD_DATA: (state, payload) => (state.dashboardData = payload)
};

export default {
    state,
    getters,
    actions,
    mutations
};
