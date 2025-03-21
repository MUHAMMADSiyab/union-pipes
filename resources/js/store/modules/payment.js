import axios from "axios";
import store from "../index";
import {
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_PAYMENT,
    GET_PAYMENTS,
    UPDATE_PAYMENT,
    DELETE_PAYMENT,
    SET_LOADING,
} from "../../mutation_constants";
import moment from "moment";

const state = {
    payments: [],
    payment: null,
};

const getters = {
    payments: (state) => state.payments,
    payment: (state) => state.payment,
};

const actions = {
    // Get payments
    async getPayments({ commit }, data) {
        try {
            const res = await axios.post(`/api/payments/get_payments`, data);

            commit(GET_PAYMENTS, res.data);
            commit(SET_LOADING, false, { root: true });
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single payment
    async getPayment({ commit }, paymentId) {
        try {
            const res = await axios.get(`/api/payments/${paymentId}`);

            commit(GET_PAYMENT, res.data);
        } catch (error) {
            console.log(error);
        }
    },

    // Add a new payment
    async addNewPayment(
        { dispatch, commit },
        {
            model,
            paymentable_id,
            transaction_type,
            amount,
            additional_amount = 0,
            deducted_amount = 0,
            payment_date,
            bank_id,
            payment_method,
            cheque_type,
            cheque_no,
            cheque_due_date,
            cheque_images,
            description,
            type, // add or edit,
            employeeId,
        }
    ) {
        try {
            const fd = new FormData();

            fd.append("model", model);
            fd.append("paymentable_id", paymentable_id);
            fd.append("transaction_type", transaction_type);
            fd.append("amount", amount);
            fd.append("deducted_amount", deducted_amount);
            fd.append("additional_amount", additional_amount);
            fd.append(
                "payment_date",
                moment(payment_date).format("Y-MM-DD HH:mm:ss")
            );
            fd.append("bank_id", bank_id);
            fd.append("payment_method", payment_method);
            fd.append("cheque_type", cheque_type);
            fd.append("cheque_no", cheque_no);
            fd.append("cheque_due_date", cheque_due_date);

            if (cheque_images.length) {
                cheque_images.forEach((image) =>
                    fd.append("cheque_images[]", image)
                );
            }

            fd.append("description", description);

            const res = await axios.post("/api/payments", fd);

            if (model === "App\\Models\\Purchase") {
                commit("purchase/PAYMENT", res.data, { root: true });
            }

            if (model === "App\\Models\\Sell") {
                commit("sell/PAYMENT", res.data, { root: true });
            }

            if (model === "App\\Models\\Salary") {
                commit("salary/PAYMENT", res.data, { root: true });

                dispatch("salary/getTotals", employeeId, { root: true });
            }

            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Changes saved successfully",
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }

            // If the payment belongs to a transaction
            if (model === "App\\Models\\Transaction") {
                const id = store.getters["transaction/recent_transaction"].id;

                await axios.delete(`/api/transactions/${id}`);
            }

            // If the payment belongs to a expense
            if (model === "App\\Models\\Expense") {
                const id = store.getters["expense/recent_expense"].id;

                await axios.delete(`/api/expenses/${id}`);
            }

            // If the payment belongs to an account entry
            if (model === "App\\Models\\Account") {
                if (type === "account_edit") {
                    const account = store.getters["account/old_account"];
                    axios.put(`/api/accounts/${paymentable_id}`, account);
                } else {
                    const id = store.getters["account/recent_account"].id;
                    await axios.delete(`/api/accounts/${id}`);
                }
            }

            // If the payment belongs to a a salary
            if (model === "App\\Models\\Salary") {
                const id = store.getters["salary/recent_salary"].id;

                await axios.delete(`/api/salaries/${id}`);
            }
        }
    },

    // Edit a payment
    async editPayment(
        { dispatch, commit },
        {
            model,
            id,
            amount,
            additional_amount = 0,
            deducted_amount = 0,
            payment_date,
            bank_id,
            payment_method,
            cheque_type,
            cheque_no,
            cheque_due_date,
            cheque_images,
            description,
            transaction_type,
            paymentable_id,
            employeeId,
        }
    ) {
        try {
            const fd = new FormData();

            fd.append("amount", amount);
            fd.append("deducted_amount", deducted_amount);
            fd.append("additional_amount", additional_amount);
            fd.append(
                "payment_date",
                moment(payment_date).format("Y-MM-DD HH:mm:ss")
            );
            fd.append("bank_id", bank_id);
            fd.append("payment_method", payment_method);
            fd.append("transaction_type", transaction_type);
            fd.append("cheque_type", cheque_type);
            fd.append("cheque_no", cheque_no);
            fd.append("cheque_due_date", cheque_due_date);
            fd.append("_method", "PUT");

            if (cheque_images.length) {
                cheque_images.forEach((image) =>
                    fd.append("cheque_images[]", image)
                );
            }

            fd.append("description", description);

            const res = await axios.post(`/api/payments/${id}`, fd);

            commit(UPDATE_PAYMENT, res.data.payment);

            if (model === "App\\Models\\Purchase") {
                commit("purchase/PAYMENT", res.data.paymentable, {
                    root: true,
                });
            }

            if (model === "App\\Models\\Sell") {
                commit("sell/PAYMENT", res.data.paymentable, {
                    root: true,
                });
            }

            if (model === "App\\Models\\Salary") {
                commit("salary/PAYMENT", res.data.paymentable, {
                    root: true,
                });

                dispatch("salary/getTotals", employeeId, { root: true });
            }

            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Changes saved successfully",
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }

            // If the payment belongs to a transaction
            if (model === "App\\Models\\Transaction") {
                const transaction =
                    store.getters["transaction/old_transaction"];
                const data = {
                    type: transaction.type,
                    title: transaction.title,
                    amount: transaction.amount,
                    description: transaction.description,
                };

                await axios.put(`/api/transactions/${paymentable_id}`, data);
            }

            // If the payment belongs to a purchase
            if (model === "App\\Models\\Purchase") {
                const purchase = store.getters["purchase/old_purchase"];
                axios.put(`/api/purchases/${paymentable_id}`, purchase);
            }

            // If the payment belongs to a an account entry
            if (model === "App\\Models\\Account") {
                const account = store.getters["account/old_account"];
                axios.put(`/api/accounts/${paymentable_id}`, account);
            }

            // If the payment belongs to a salary
            if (model === "App\\Models\\Salary") {
                const salary = store.getters["salary/old_salary"];
                const data = {
                    month: salary.month,
                    date: salary.date,
                    total_paid: salary.total_paid,
                    additional_amount: salary.additional_amount,
                    deducted_amount: salary.deducted_amount,
                };

                await axios.put(`/api/salaries/${paymentable_id}`, data);
            }
        }
    },

    // Delete a payment
    async deletePayment({ dispatch, commit }, { id, model, employeeId }) {
        try {
            const res = await axios.delete(`/api/payments/${id}`);

            commit(DELETE_PAYMENT, id);

            if (model === "App\\Models\\Purchase") {
                commit("purchase/PAYMENT", res.data, {
                    root: true,
                });
            }

            if (model === "App\\Models\\Sell") {
                commit("sell/PAYMENT", res.data, {
                    root: true,
                });
            }

            if (model === "App\\Models\\Salary") {
                commit("salary/PAYMENT", res.data, {
                    root: true,
                });

                dispatch("salary/getTotals", employeeId, { root: true });
            }

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Payment deleted successfully",
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },
};

const mutations = {
    GET_PAYMENTS: (state, payload) => (state.payments = payload),

    GET_PAYMENT: (state, payload) => (state.payment = payload),

    UPDATE_PAYMENT: (state, payload) => {
        const index = state.payments.findIndex(
            (payment) => payment.id === payload.id
        );
        state.payments.splice(index, 1, payload);
    },

    DELETE_PAYMENT: (state, payload) => {
        const index = state.payments.findIndex(
            (payment) => payment.id === payload
        );
        state.payments.splice(index, 1);
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
