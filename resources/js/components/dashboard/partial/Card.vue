<template>
    <div class="d-flex align-center">
        <v-sheet :color="color" class="pa-2" rounded>
            <v-icon x-large color="white">{{ icon }}</v-icon>
        </v-sheet>
        <div class="right__content pa-1 ml-1">
            <span class="text-caption blue-grey--text lighten-1">{{
                title
            }}</span>
            <h4 class="text-h5 blue-grey--text darken-3">
                {{ money(number) }}
            </h4>
        </div>
    </div>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";
export default {
    props: ["color", "icon", "title", "number"],

    mixins: [CurrencyMixin],
};
</script>
