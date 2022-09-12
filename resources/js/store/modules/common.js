import { SET_PAYMENT_SETTING } from "../../mutation_constants";

const state = {
    permissions: [],
    permission: null,
    validationErrors: null,
    paymentSetting: null,
    loading: true,
};

const getters = {
    validationErrors: (state) => state.validationErrors,
    paymentSetting: (state) => state.paymentSetting,
    loading: (state) => state.loading,
};

const actions = {
    async getProducts({ commit }) {
        try {
            const res = await axios.get("/api/products");

            commit(SET_PAYMENT_SETTING, res.data);
        } catch (error) {
            commit(SET_PAYMENT_SETTING, null);
            console.log(error);
        }
    },
};

const mutations = {
    SET_LOADING: (state, payload) => (state.loading = payload),

    SET_VALIDATION_ERRORS: (state, payload) =>
        (state.validationErrors = payload),

    CLEAR_VALIDATION_ERRORS: (state, payload) =>
        (state.validationErrors = null),

    SET_PAYMENT_SETTING: (state, payload) => (state.paymentSetting = payload),
};

export default {
    state,
    getters,
    actions,
    mutations,
};
