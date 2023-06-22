import { SET_PRINT_MODE } from "../../mutation_constants";

const state = {
    printMode: false
};

const getters = {
    printMode: state => state.printMode
};

const actions = {
    async setPrintMode({ commit }, mode) {
        await commit(SET_PRINT_MODE, mode);
    }
};

const mutations = {
    SET_PRINT_MODE: (state, payload) => (state.printMode = payload)
};

export default {
    state,
    getters,
    actions,
    mutations
};
