<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Gate Passes</h5>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="gate_passes"
                        class="elevation-1"
                        item-key="id"
                        :search="search"
                        :items-per-page="perPage"
                        :loading="loading"
                        :show-select="can('gate_pass_delete') && !printMode"
                        loading-text="Loading gate passes..."
                        :footer-props="footerProps"
                        v-model="selectedItems"
                        dense
                    >
                        <template slot="item.sno" slot-scope="props">{{
                            props.index + 1
                        }}</template>

                        <template slot="item.sell" slot-scope="props">
                            <v-icon
                                v-if="hasSell(props.item)"
                                class="green--text"
                                >mdi-check</v-icon
                            >
                            <v-icon v-else class="red--text">mdi-close</v-icon>
                        </template>

                        <!-- Top -->
                        <template v-slot:top v-if="!printMode">
                            <v-btn
                                color="error"
                                small
                                :disabled="!selectedItems.length"
                                class="ma-2 text-right"
                                @click="deleteMultiple"
                                v-if="can('gate_pass_delete')"
                                ><v-icon left>mdi-trash-can-outline</v-icon>
                                Delete Selected</v-btn
                            >

                            <v-btn
                                color="success"
                                small
                                link
                                to="/gate_passes/add"
                                class="ma-2 text-right"
                                v-if="can('gate_pass_create')"
                            >
                                <v-icon left>mdi-receipt</v-icon>
                                New Gate Pass</v-btn
                            >

                            <v-text-field
                                v-model="search"
                                placeholder="Search"
                                class="mx-4"
                                append-icon="mdi-magnify"
                            ></v-text-field>
                        </template>

                        <!-- Actions -->
                        <template slot="item.actions" slot-scope="props">
                            <v-btn
                                x-small
                                text
                                color="indigo"
                                :to="`/sells/add?gate_pass_id=${props.item.id}`"
                                title="Add Sell"
                                v-if="can('sell_create')"
                                :disabled="hasSell(props.item)"
                            >
                                <v-icon small>mdi-plus</v-icon>
                            </v-btn>

                            <v-btn
                                x-small
                                text
                                color="primary"
                                :to="`/gate_passes/${props.item.id}`"
                                title="Print Gate Pass"
                                v-if="can('gate_pass_show')"
                            >
                                <v-icon small>mdi-printer</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setGatePassId(props.item.id)"
                                title="Delete"
                                v-if="can('gate_pass_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>

                    <!-- Confirmation -->
                    <Confirmation
                        ref="confirmationComponent"
                        :id="gatePassId"
                        @confirmDeletion="
                            gatePassId
                                ? handleGatePassDelete()
                                : handleMultipleTransactionsDelete()
                        "
                    />
                </v-col>
            </v-row>

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";

export default {
    mixins: [DatatableMixin],

    components: {
        Navbar,
        Confirmation,
    },

    data() {
        return {
            headers: [
                { text: "S#", value: "sno" },
                { text: "Date", value: "date" },
                { text: "Receiver", value: "receiver" },
                { text: "Vehicle No.", value: "vehicle_no" },
                { text: "Sell Entry", value: "sell" },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],
            gatePassId: null,
        };
    },

    methods: {
        hasSell(gatePass) {
            return gatePass.sell ? true : false;
        },

        ...mapActions({
            getGatePasses: "gate_pass/getGatePasses",
            deleteGatePass: "gate_pass/deleteGatePass",
            deleteMultipleGatePasses: "gate_pass/deleteMultipleGatePasses",
        }),

        setGatePassId(id) {
            this.gatePassId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleGatePassDelete() {
            await this.deleteGatePass(this.gatePassId);
            this.gatePassId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleGatePassesDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleGatePasses(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            gate_passes: "gate_pass/gate_passes",
            loading: "loading",
        }),
    },

    mounted() {
        // remove actions if no access is given
        if (!this.can("gate_pass_edit") && !this.can("gate_pass_delete")) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }
        this.getGatePasses();
    },
};
</script>
