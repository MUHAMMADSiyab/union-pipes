<template>
  <v-card>
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <h5 class="text-uppercase light--text">Vehicle Details</h5>
          <v-simple-table>
            <tbody>
              <tr>
                <td>Registration No.</td>
                <td class="font-weight-bold">{{ vehicle.registration_no }}</td>
              </tr>
              <tr>
                <td>Chassis No.</td>
                <td class="font-weight-bold">{{ vehicle.chassis_no }}</td>
              </tr>
              <tr>
                <td>Contractor Name</td>
                <td class="font-weight-bold">{{ vehicle.contractor_name }}</td>
              </tr>
            </tbody>
          </v-simple-table>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: ["vehicle"],
};
</script>
