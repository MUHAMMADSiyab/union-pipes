import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_BULK_PAYMENTS,
    DELETE_BULK_PAYMENT,
    GET_BULK_PAYMENT,
    NEW_BULK_PAYMENT,
} from "../../mutation_constants";
import moment from "moment";

const state = {
    bulk_payments: [],
    bulk_payment: null,
    loading: false,
    total: 0,
};

const getters = {
    bulk_payments: (state) => state.bulk_payments,
    bulk_payment: (state) => state.bulk_payment,
    loading: (state) => state.loading,
    total: (state) => state.total,
};

const actions = {
    // Add bulk payment
    async addBulkPayment(
        { dispatch, commit },
        {
            type,
            customer_id,
            company_id,
            amount,
            date,
            bank_id,
            payment_method,
            cheque_type,
            cheque_no,
            cheque_due_date,
            cheque_images,
            description,
        }
    ) {
        try {
            const fd = new FormData();

            fd.append("type", type);
            fd.append("customer_id", customer_id);
            fd.append("company_id", company_id);
            fd.append("amount", amount);
            fd.append("date", moment(date).format("Y-MM-DD HH:mm:ss"));
            fd.append("bank_id", bank_id);
            fd.append("payment_method", payment_method);
            fd.append("cheque_type", cheque_type);
            fd.append("cheque_no", cheque_no);
            fd.append("cheque_due_date", cheque_due_date);
            fd.append("description", description);

            if (cheque_images.length) {
                cheque_images.forEach((image) =>
                    fd.append("cheque_images[]", image)
                );
            }

            const res = await axios.post("/api/bulk_payments", fd);

            commit(NEW_BULK_PAYMENT, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Bulk payment added successfully",
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

    // Get bulk payments
    async getBulkPayments(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc, search }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "id";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/bulk_payments?page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}&search=${search}`
            );
            commit(SET_LOADING, false);
            commit(GET_BULK_PAYMENTS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Get a single bulk payment
    async getBulkPayment({ commit }, id) {
        try {
            const res = await axios.get(`/api/bulk_payments/${id}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_BULK_PAYMENT, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Search bulk payments
    async searchBulkPayments(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc, search }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "id";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/search_bulk_payments?search=${search}&page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_BULK_PAYMENTS, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Update bulk_payment

    // Delete bulk payment
    async deleteBulkPayment({ dispatch, commit }, bulk_payment_id) {
        try {
            const res = await axios.delete(
                `/api/bulk_payments/${bulk_payment_id}`
            );

            commit(DELETE_BULK_PAYMENT, bulk_payment_id);

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

    // Delete multiple bulk payments
    // async deleteMultipleBulkPayments({ dispatch, commit }, ids) {
    //     try {
    //         const res = await axios.delete(
    //             `/api/bulk_payments/delete_multiple`,
    //             {
    //                 headers: {},
    //                 data: { ids },
    //             }
    //         );

    //         commit(DELETE_BULK_PAYMENTS, ids);

    //         return dispatch(
    //             "alert/setAlert",
    //             {
    //                 type: "success",
    //                 message: res.data.success,
    //             },
    //             { root: true }
    //         );
    //     } catch (error) {
    //         console.log(error);
    //     }
    // },
};

const mutations = {
    SET_LOADING: (state, payload) => (state.loading = payload),

    GET_BULK_PAYMENTS: (state, payload) => {
        state.bulk_payments = payload.data;
        state.total = payload.total;
    },

    GET_BULK_PAYMENT: (state, payload) => (state.bulk_payment = payload),

    NEW_BULK_PAYMENT: (state, payload) => {
        state.recent_bulk_payment = payload;
    },

    OLD_BULK_PAYMENT: (state, payload) => {
        state.old_bulk_payment = payload;
    },

    DELETE_BULK_PAYMENT: (state, payload) => {
        const index = state.bulk_payments.findIndex(
            (bulk_payment) => bulk_payment.id === payload
        );
        state.bulk_payments.splice(index, 1);
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
