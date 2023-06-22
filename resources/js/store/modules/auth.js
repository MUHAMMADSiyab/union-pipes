import axios from "axios";
import {
    SET_TOKEN,
    SET_USER,
    SET_USER_ABILITIES,
    SET_AUTH_ERROR,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS
} from "../../mutation_constants";

const state = {
    user: null,
    token: null,
    user_abilities: [],
    error: null
};

const getters = {
    user: state => state.user,
    isAuthenticated: state => state.user,
    user_abilities: state => state.user_abilities,
    error: state => state.error
};

const actions = {
    // Attempt login
    async attemptLogin({ dispatch, commit }, credentials) {
        try {
            const res = await axios.post("/api/attempt_login", credentials);

            commit(SET_AUTH_ERROR, null);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch("getCurrentUser", res.data.token);
        } catch (error) {
            commit(SET_AUTH_ERROR, error.response.data);

            if (error.response.status === 422) {
                return commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true
                });
            }

            if (error.response.data.error) {
                dispatch(
                    "alert/setAlert",
                    {
                        type: "error",
                        message: error.response.data.error
                    },
                    { root: true }
                );
            }
        }
    },

    // Get the currently authenticated user
    async getCurrentUser({ dispatch, commit, state }, token) {
        commit(SET_TOKEN, token);
        // Request current user if authenticated
        if (state.token) {
            try {
                const res = await axios.get("/api/me");

                commit(SET_USER, res.data);

                return dispatch("getUserAbilities");
            } catch (error) {
                console.log(error);
            }
        }
    },

    // Get user abilities
    async getUserAbilities({ commit }) {
        try {
            const res = await axios.get("/api/abilities");
            commit(SET_USER_ABILITIES, res.data);
        } catch (error) {
            console.log(error);
        }
    },

    // Log the user out
    async logout({ commit }) {
        try {
            await axios.post("/api/logout");

            // Remove the token & set the user to null
            localStorage.removeItem("token");
            commit(SET_USER, null);
            commit(SET_USER_ABILITIES, []);
        } catch (error) {
            console.log(error);
        }
    }
};

const mutations = {
    SET_AUTH_ERROR: (state, payload) => (state.error = payload),

    SET_TOKEN: (state, payload) => (state.token = payload),

    SET_USER: (state, payload) => (state.user = payload),

    SET_USER_ABILITIES: (state, payload) => (state.user_abilities = payload)
};

export default {
    state,
    getters,
    actions,
    mutations
};
