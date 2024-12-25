import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_PARTNER_WITHDRAWALS,
    UPDATE_PARTNER_WITHDRAWAL,
    DELETE_PARTNER_WITHDRAWAL,
    DELETE_PARTNER_WITHDRAWALS,
    GET_PARTNER_WITHDRAWAL,
    NEW_PARTNER_WITHDRAWAL,
    OLD_PARTNER_WITHDRAWAL,
} from "../../mutation_constants";

const state = {
    partner_withdrawals: [],
    partner_withdrawal: null,
    recent_partner_withdrawal: null,
    old_partner_withdrawal: null,
    loading: false,
    total: 0,
};

const getters = {
    partner_withdrawals: (state) => state.partner_withdrawals,
    partner_withdrawal: (state) => state.partner_withdrawal,
    recent_partner_withdrawal: (state) => state.recent_partner_withdrawal,
    old_partner_withdrawal: (state) => state.old_partner_withdrawal,
    loading: (state) => state.loading,
    total: (state) => state.total,
};

const actions = {
    // Add partner withdrawal
    async addPartnerWithdrawal({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/partner_withdrawals", data);

            commit(NEW_PARTNER_WITHDRAWAL, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }

            console.log(error);
        }
    },

    // Get partner withdrawals
    async getPartnerWithdrawals(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "id";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/partner_withdrawals?page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_PARTNER_WITHDRAWALS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Get a single partner withdrawal
    async getPartnerWithdrawal({ commit }, id) {
        try {
            const res = await axios.get(`/api/partner_withdrawals/${id}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PARTNER_WITHDRAWAL, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Search partner_withdrawals
    async searchPartnerWithdrawals(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc, search }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "id";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/search_partner_withdrawals?search=${search}&page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_PARTNER_WITHDRAWALS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Update partner_withdrawal
    async updatePartnerWithdrawal({ commit }, data) {
        try {
            const res = await axios.put(
                `/api/partner_withdrawals/${data.id}`,
                data
            );

            commit(
                UPDATE_PARTNER_WITHDRAWAL,
                res.data.updated_partner_withdrawal
            );
            commit(OLD_PARTNER_WITHDRAWAL, res.data.old_partner_withdrawal);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
            console.log(error);
        }
    },

    // Delete partner_withdrawal
    async deletePartnerWithdrawal({ dispatch, commit }, partner_withdrawalId) {
        try {
            const res = await axios.delete(
                `/api/partner_withdrawals/${partner_withdrawalId}`
            );

            commit(DELETE_PARTNER_WITHDRAWAL, partner_withdrawalId);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },

    // Delete multiple partner_withdrawals
    async deleteMultiplePartnerWithdrawals({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(
                `/api/partner_withdrawals/delete_multiple`,
                {
                    headers: {},
                    data: { ids },
                }
            );

            commit(DELETE_PARTNER_WITHDRAWALS, ids);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },
};

const mutations = {
    SET_LOADING: (state, payload) => (state.loading = payload),

    GET_PARTNER_WITHDRAWALS: (state, payload) => {
        state.partner_withdrawals = payload.data;
        state.total = payload.total;
    },

    GET_PARTNER_WITHDRAWAL: (state, payload) =>
        (state.partner_withdrawal = payload),

    NEW_PARTNER_WITHDRAWAL: (state, payload) => {
        state.recent_partner_withdrawal = payload;
    },

    OLD_PARTNER_WITHDRAWAL: (state, payload) => {
        state.old_partner_withdrawal = payload;
    },

    UPDATE_PARTNER_WITHDRAWAL: (state, payload) => {
        const index = state.partner_withdrawals.findIndex(
            (partner_withdrawal) => partner_withdrawal.id === payload.id
        );
        state.partner_withdrawals.splice(index, 1, payload);
    },

    DELETE_PARTNER_WITHDRAWAL: (state, payload) => {
        const index = state.partner_withdrawals.findIndex(
            (partner_withdrawal) => partner_withdrawal.id === payload
        );
        state.partner_withdrawals.splice(index, 1);
    },

    DELETE_PARTNER_WITHDRAWALS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.partner_withdrawals.findIndex(
                (partner_withdrawal) => partner_withdrawal.id === id
            );
            state.partner_withdrawals.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
