<template>
    <v-container
        fluid
        fill-height
        style="
            background: url('storage/common/login-bg.jpg') no-repeat
                center/cover;
        "
    >
        <v-row justify="center">
            <v-col xl="4" lg="4" md="5" sm="8" xs="12">
                <v-card
                    elevation="1"
                    :loading="formLoading"
                    class="pa-3 login-card"
                >
                    <h4 class="text-subtitle text-center mt-3 grey--text">
                        User Account Login
                    </h4>

                    <v-card-text class="mt-2">
                        <v-form @submit.prevent="handleSubmit">
                            <small
                                class="red--text"
                                v-if="validation.hasErrors()"
                                v-text="validation.getMessage('email')"
                            ></small>
                            <v-text-field
                                name="email"
                                label="Email"
                                id="email"
                                type="email"
                                v-model="data.email"
                                prepend-inner-icon="mdi-email"
                                :required="true"
                                aria-required="true"
                            ></v-text-field>

                            <small
                                class="red--text"
                                v-if="validation.hasErrors()"
                                v-text="validation.getMessage('password')"
                            ></small>
                            <v-text-field
                                name="password"
                                label="Password"
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="data.password"
                                prepend-inner-icon="mdi-account-key"
                                :append-icon="
                                    showPassword ? 'mdi-eye' : 'mdi-eye-off'
                                "
                                @click:append="showPassword = !showPassword"
                                required="true"
                                aria-required="true"
                            ></v-text-field>

                            <v-btn
                                type="submit"
                                color="primary"
                                class="mt-2"
                                :disabled="formLoading"
                            >
                                Login
                            </v-btn>
                        </v-form>

                        <alert />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";

export default {
    mixins: [ValidationMixin],

    data() {
        return {
            formLoading: false,
            showPassword: false,
            data: {
                email: "",
                password: "",
            },
        };
    },

    methods: {
        ...mapActions({
            attemptLogin: "auth/attemptLogin",
            getAppSetting: "setting/getAppSetting",
        }),

        async handleSubmit() {
            this.formLoading = true;

            await this.attemptLogin(this.data);

            this.formLoading = false;

            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.validation.setMessages({});

                if (!this.authError) {
                    this.data.email = "";
                    this.data.password = "";
                    this.$router.replace({ name: "dashboard" });
                }
            }
        },
    },

    computed: {
        ...mapGetters({
            user: "auth/user",
            authError: "auth/error",
            app_setting: "setting/app_setting",
            validationErrors: "validationErrors",
        }),
    },

    mounted() {
        this.getAppSetting();
    },
};
</script>
