<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title
                            >Edit Raw Material</v-card-title
                        >
                        <v-card-subtitle
                            >Edit Raw Material Entries</v-card-subtitle
                        >

                        <v-card-text class="mt-2">
                            <v-form @submit.prevent="update">
                                <v-row>
                                    <v-col
                                        xl="3"
                                        lg="3"
                                        md="3"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('month')
                                            "
                                        ></small>
                                        <v-menu
                                            max-width="290px"
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="data.month"
                                                    v-on="on"
                                                    label="Month"
                                                    prepend-inner-icon="mdi-calendar"
                                                    dense
                                                    filled
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="data.month"
                                                label="Month"
                                                type="month"
                                                no-title
                                                outlined
                                                dense
                                                show-current
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>
                                </v-row>

                                <div
                                    v-for="(entry, index) in data.entries"
                                    :key="index"
                                    class="d-flex align-center mb-2 mt-4"
                                >
                                    <v-text-field
                                        v-model="entry.quality"
                                        label="Quality"
                                        small
                                        class="mr-2"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.quality`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model="entry.bag"
                                        label="Bag"
                                        type="number"
                                        small
                                        class="mr-2"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.bag`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model="entry.kg"
                                        label="Kg"
                                        type="number"
                                        small
                                        class="mr-2"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.kg`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model="entry.weight"
                                        label="Weight"
                                        type="number"
                                        small
                                        class="mr-2"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.weight`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model="entry.rate"
                                        label="Rate"
                                        type="number"
                                        small
                                        class="mr-2"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.rate`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model="entry.amount"
                                        label="Amount"
                                        type="number"
                                        small
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.amount`
                                            )
                                        "
                                    ></small>

                                    <v-btn
                                        icon
                                        @click.prevent="removeEntry(index)"
                                    >
                                        <v-icon>mdi-minus-circle</v-icon>
                                    </v-btn>
                                </div>

                                <v-btn
                                    color="green white--text"
                                    @click.prevent="addEntry"
                                    ><v-icon>mdi-plus</v-icon> Add Entry</v-btn
                                >
                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="float-end"
                                    >Save</v-btn
                                >
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
    mixins: [ValidationMixin],

    components: { Navbar },

    data() {
        return {
            formLoading: false,
            data: {
                id: null,
                month: "",
                entries: [
                    {
                        quality: "",
                        bag: 0,
                        kg: 0,
                        weight: 0,
                        rate: 0,
                        amount: 0,
                    },
                ],
            },
        };
    },

    methods: {
        ...mapActions({
            getRawMaterial: "raw_material/getRawMaterial",
            updateRawMaterial: "raw_material/updateRawMaterial",
        }),

        async update() {
            this.formLoading = true;

            await this.updateRawMaterial(this.data);

            this.formLoading = false;

            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.validation.setMessages({});

                return this.$router.push({ name: "raw_materials" });
            }
        },

        addEntry() {
            this.data.entries.push({
                quality: "",
                bag: 0,
                kg: 0,
                weight: 0,
                rate: 0,
                amount: 0,
            });
        },

        removeEntry(index) {
            this.data.entries.splice(index, 1);
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
            raw_material: "raw_material/raw_material",
        }),
    },

    watch: {
        "data.entries": {
            handler(updatedEntries) {
                updatedEntries.forEach((entry) => {
                    const weight = entry.bag * entry.kg;
                    const amount = weight * entry.rate;
                    entry.weight = weight;
                    entry.amount = amount;
                });
            },
            deep: true,
        },
    },

    async mounted() {
        await this.getRawMaterial(this.$route.params.id);

        if (!this.raw_material) {
            return this.$router.push({ name: "not_found" });
        }

        this.data.id = this.raw_material.id;
        this.data.month = this.raw_material.month.slice(0, 7);
        this.data.entries = this.raw_material.entries;
    },
};
</script>
