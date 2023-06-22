import { v4 } from "uuid";
import { SET_ALERT, HIDE_ALERT } from "../../mutation_constants";

const state = {
    data: null
};

const getters = {
    data: state => state.data
};

const actions = {
    async setAlert({ commit }, { type, message }) {
        const alert = {
            id: v4(),
            type,
            message
        };

        commit(SET_ALERT, alert);

        setTimeout(() => {
            commit(HIDE_ALERT);
        }, 5000);
    }
};

const mutations = {
    SET_ALERT: (state, payload) => (state.data = payload),

    HIDE_ALERT: state => (state.data = null)
};

export default {
    state,
    getters,
    actions,
    mutations
};
