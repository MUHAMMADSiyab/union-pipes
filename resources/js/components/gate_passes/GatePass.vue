<template>
    <div class="container" v-if="app_setting && gate_pass">
        <v-btn
            color="primary"
            class="d-print-none"
            title="Go Back"
            @click="$router.go(-1)"
            ><v-icon centered>mdi-keyboard-backspace</v-icon></v-btn
        >

        <template v-for="(pass, index) in [1, 2, 3]">
            <table
                :key="index"
                cellspacing="0"
                width="100%"
                style="
                    font-size: small;
                    margin-bottom: 10px;
                    position: relative;
                "
            >
                <h3 class="title">GATE PASS</h3>
                <h4 class="serial-number">{{ gate_pass.number }}</h4>

                <tr>
                    <th colspan="4">
                        <h2 class="text-center">{{ app_setting.app_name }}</h2>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
                        <h5 class="text-center">SIZE: 63MM to 16IN</h5>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
                        <h5 class="text-center">
                            AL-QARYAN INDUSTRIES (PVT) LTD.
                        </h5>
                    </th>
                </tr>

                <tr>
                    <td colspan="2">
                        M/S or MR.
                        <span
                            class="text-decoration-underline font-weight-bold"
                            >{{ gate_pass.receiver }}</span
                        >
                    </td>
                    <td>
                        VEHICLE NO.
                        <span
                            class="text-decoration-underline font-weight-bold"
                            >{{ gate_pass.vehicle_no }}</span
                        >
                    </td>
                    <td>
                        DRIVER NAME
                        <span
                            class="text-decoration-underline font-weight-bold"
                            >{{ gate_pass.driver_name }}</span
                        >
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        DATE
                        <span
                            class="text-decoration-underline font-weight-bold"
                            >{{ gate_pass.date }}</span
                        >
                    </td>
                    <td>
                        IN TIME
                        <span
                            class="text-decoration-underline font-weight-bold"
                            >{{ gate_pass.in_time }}</span
                        >
                    </td>
                    <td>
                        OUT TIME
                        <span
                            class="text-decoration-underline font-weight-bold"
                            >{{ gate_pass.out_time }}</span
                        >
                    </td>
                </tr>
            </table>

            <table
                :key="`${index}-particulars-table`"
                cellspacing="0"
                width="100%"
                style="
                    font-size: small;
                    margin-bottom: 10px;
                    position: relative;
                "
            >
                <tr class="text-left bordered-row">
                    <th width="10">S#</th>
                    <th>Particulars</th>
                    <th>Qty</th>
                    <th>Remarks</th>
                </tr>
                <tr
                    v-for="(item, i) in gate_pass.items"
                    :key="i"
                    class="bordered-row"
                >
                    <td width="10">{{ i + 1 }}</td>
                    <td>{{ item.particular }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ item.remarks }}</td>
                </tr>
            </table>
            <table :key="`${index}-footer-table`" style="font-size: small">
                <tr>
                    <td style="padding-top: 18px">
                        Prepared By _______________
                    </td>
                    <td style="padding-top: 18px">
                        Checked By _______________
                    </td>
                    <td style="padding-top: 18px">Receiver _______________</td>
                    <td style="padding-top: 18px">
                        Approved By _______________
                    </td>
                </tr>
            </table>
            <div
                :key="`${index}-trimline`"
                v-if="index !== 2"
                style="
                    margin-block: 18px;
                    width: 100%;
                    height: 3px;
                    border-top: 3px dashed #d1d1d1;
                "
            ></div>
        </template>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    data() {
        return {};
    },

    methods: {
        ...mapActions({
            getAppSetting: "setting/getAppSetting",
            getGatePass: "gate_pass/getGatePass",
        }),
    },

    computed: {
        ...mapGetters({
            app_setting: "setting/app_setting",
            gate_pass: "gate_pass/gate_pass",
        }),
    },

    async mounted() {
        await Promise.all([
            this.getAppSetting(),
            this.getGatePass(this.$route.params.id),
        ]);

        if (!this.gate_pass) {
            return this.$router.push({ name: "not_found" });
        }

        this.print();
    },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap");

.container {
    padding: 5px;
    background: #fff;
}
table tr td {
    padding: 5px;
}
.title {
    position: absolute;
    width: fit-content;
    top: 5px;
    margin-block: 10px;
    padding-inline: 6px;
    font-size: 0.9rem !important;
    background: #000;
    color: #fff;
}
.serial-number {
    position: absolute;
    right: 5px;
    top: 5px;
    font-family: "Chakra Petch", sans-serif;
    font-weight: 500;
    font-size: 1rem;
    color: #5e5e5e;
    border-bottom: 2px dotted #c4c4c4;
}
.bordered-row th,
.bordered-row td {
    padding: 5px;
    border: 1px solid gray;
}

h5 {
    font-weight: normal !important;
}
</style>
