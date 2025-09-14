<script setup>
    import Stepper from 'primevue/stepper'
    import StepList from 'primevue/steplist'
    import StepPanels from 'primevue/steppanels'
    import Step from 'primevue/step'
    import StepPanel from 'primevue/steppanel'
    import { Button } from 'primevue'
    import TermsAndConditions from '@/Components/TermsAndConditions.vue'
    import AppointmentForm from '@/Components/AppointmentForm.vue'
    import { useForm } from '@inertiajs/vue3'
    import { onMounted, ref, watch } from 'vue'
    import TestTypeAndSchedule from '../../Components/TestTypeAndSchedule.vue'

    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
    import AppointmentDetailsModal from '@/Components/modal/AppointmentDetailsModal.vue'
    import { formateBirthDate } from '@/helpers/formatter'

    const props = defineProps({
        test_categories: Array,
        appointment_schedules: Array,
    })

    const toast = useToast()
    const showAppointmentDetails = ref(false)
    const selectedScheduleRef = ref('')

    // INERTIA FORM INIATILIZATION
    const form = useForm({
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '', // MAKE THIS NULLABLE IN THE BACKEND PARA SA MGA ARTE NA PANEL
        gender: '',
        birthdate: '',
        selected_schedule_id: -1,
        selected_time_slot_id: -1,
        selected_type_ids: [],
    })

    // FORM SUBMISSION
    function submitForm() {
        form.birthdate = formateBirthDate(form.birthdate)

        console.log("form data boss: ", form);
        form.post(route('store.services.appointment'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Appointment Request Submitted',
                    detail: 'Your appointment request has been received. Please monitor your email for confirmation and the appointment reference code from our administrator.',
                    life: 4000,
                    closable: true,
                })

                setTimeout(() => {
                    window.location.href = '/services/appointment'
                }, 4100)
            },
        })
    }

    // CURRENT STEP OF THE STEPPER
    const currentStep = ref('1')

    onMounted(() => {
        console.log('test_categories: ', props.test_categories)
    })
</script>

<template>
    <div
        class="h-screen flex flex-col items-center justify-center bg-gradient-to-r from-green-400 to-gray-100"
    >
        <!-- COMPANY LOGO -->
        <div class="p-8">
            <img alt="Company Logo" src="/img/lexa-logo-removedbg.png" />
        </div>

        <Stepper
            :value="currentStep"
            @update:value="(val) => (currentStep = val)"
            class="basis-[50rem] overflow-y-auto"
        >
            <StepList>
                <Step value="1">Terms & Conditions</Step>
                <Step value="2">Personal Information</Step>
                <Step value="3">Test Type & Schedule</Step>
            </StepList>

            <StepPanels>
                <StepPanel v-slot="{ activateCallback }" value="1">
                    <div class="flex flex-col h-full">
                        <div class="flex-auto flex justify-center items-center font-medium">
                            <TermsAndConditions />
                        </div>
                    </div>
                    <div class="flex py-4 pr-3 justify-end">
                        <Button
                            label="Next"
                            icon="pi pi-arrow-right"
                            iconPos="right"
                            @click="
                                () => {
                                    currentStep = '2'
                                    activateCallback('2')
                                }
                            "
                        />
                    </div>
                </StepPanel>

                <StepPanel v-slot="{ activateCallback }" value="2">
                    <div class="flex flex-col h-full">
                        <div class="flex-auto flex justify-center items-center font-medium">
                            <AppointmentForm :form="form" />
                        </div>
                    </div>
                    <div class="flex py-4 pr-3 justify-between">
                        <Button
                            label="Back"
                            severity="secondary"
                            icon="pi pi-arrow-left"
                            @click="
                                () => {
                                    currentStep = '1'
                                    activateCallback('1')
                                }
                            "
                        />
                        <Button
                            label="Next"
                            icon="pi pi-arrow-right"
                            iconPos="right"
                            @click="
                                () => {
                                    currentStep = '3'
                                    activateCallback('3')
                                }
                            "
                        />
                    </div>
                </StepPanel>

                <StepPanel v-slot="{ activateCallback }" value="3">
                    <div class="flex flex-col h-full">
                        <div class="flex-auto flex justify-center items-center font-medium">
                            <TestTypeAndSchedule
                                :test_categories="test_categories"
                                :appointment_schedules="appointment_schedules"
                                :form="form"
                            />
                        </div>
                    </div>
                    <div class="flex justify-between py-4 pr-3 mx-3">
                        <Button
                            label="Back"
                            severity="secondary"
                            icon="pi pi-arrow-left"
                            @click="
                                () => {
                                    currentStep = '2'
                                    activateCallback('2')
                                }
                            "
                        />

                        <button
                            v-if="currentStep === '3'"
                            @click="submitForm"
                            :class="[
                                'block w-[15%] rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-white',
                                form.processing ? 'bg-gray-400' : 'bg-green-600 hover:bg-green-500',
                            ]"
                            :disabled="form.processing"
                        >
                            Submit
                        </button>
                    </div>
                </StepPanel>
            </StepPanels>
        </Stepper>

        <Toast />

        <AppointmentDetailsModal
            v-if="showAppointmentDetails"
            :selectedSchedule="selectedScheduleRef"
            @close="showAppointmentDetails = false"
        />
    </div>
</template>
