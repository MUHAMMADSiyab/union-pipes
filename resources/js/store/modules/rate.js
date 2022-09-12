import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_CURRENT_RATES,
    UPDATE_RATES,
    GET_RATES,
} from "../../mutation_constants";

const state = {
    rates: [],
    rate: null,
    current_rates: [],
};

const getters = {
    rates: (state) => state.rates,
    rate: (state) => state.rate,
    current_rates: (state) => state.current_rates,
};

const actions = {
    async getCurrentRates({ commit }) {
        try {
            const res = await axios.get("/api/rates/current");

            commit(SET_LOADING, false, { root: true });
            commit(GET_CURRENT_RATES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get rates
    async getRates({ commit }) {
        try {
            const res = await axios.get("/api/rates");

            commit(SET_LOADING, false, { root: true });
            commit(GET_RATES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update rate
    async updateRates({ dispatch, commit }, data) {
        try {
            const res = await axios.post(`/api/rates/update`, data);

            commit(UPDATE_RATES, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Rates updated successfully",
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
};

const mutations = {
    GET_RATES: (state, payload) => (state.rates = payload),

    GET_CURRENT_RATES: (state, payload) => (state.current_rates = payload),

    UPDATE_RATES: (state, payload) =>
        (state.rates = [...payload, ...state.rates]),
};

export default {
    state,
    getters,
    actions,
    mutations,
};
