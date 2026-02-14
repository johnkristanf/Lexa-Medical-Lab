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
        priority_types: Array,
    })

    const genders = ref([
        { name: 'MALE', code: 'male' },
        { name: 'FEMALE', code: 'female' },
    ])

    const toast = useToast()
    const showAppointmentDetails = ref(false)
    const selectedScheduleRef = ref('')
    const scheduleComponentRef = ref(null)

    // INERTIA FORM INIATILIZATION
    const form = useForm({
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '', // MAKE THIS NULLABLE IN THE BACKEND PARA SA MGA ARTE NA PANEL
        phone: '',
        address: '',
        gender: null,
        birthdate: '',
        priority_type: null, // INITIAL VALUE SET REGULAR PATIENT
        selected_schedule_id: -1,
        selected_time_slot_id: -1,
        selected_type_ids: [],
    })

    // Validation error state
    const validationErrors = ref({
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        address: '',
        gender: '',
        patient_type: '',
        birthdate: '',
    })

    // Validate required fields
    const validateForm = () => {
        let isValid = true
        validationErrors.value = {
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            address: '',
            gender: '',
            patient_type: '',
            birthdate: '',
        }

        // Validate first name
        if (!form.first_name || form.first_name.trim() === '') {
            validationErrors.value.first_name = 'First name is required'
            isValid = false
        }

        // Validate last name
        if (!form.last_name || form.last_name.trim() === '') {
            validationErrors.value.last_name = 'Last name is required'
            isValid = false
        }

        // Validate email
        if (!form.email || form.email.trim() === '') {
            validationErrors.value.email = 'Email is required'
            isValid = false
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
            validationErrors.value.email = 'Please enter a valid email address'
            isValid = false
        }

        // Validate phone
        if (!form.phone || form.phone.trim() === '') {
            validationErrors.value.phone = 'Phone number is required'
            isValid = false
        } else if (!/^[0-9]{10}$/.test(form.phone)) {
            validationErrors.value.phone = 'Please enter a valid 10-digit phone number'
            isValid = false
        }

        // Validate address
        if (!form.address || form.address.trim() === '') {
            validationErrors.value.address = 'Address is required'
            isValid = false
        }

        // Validate gender
        if (!form.gender || !form.gender.code) {
            validationErrors.value.gender = 'Please select a sex'
            isValid = false
        }

        // Validate patient type
        if (!form.priority_type || !form.priority_type.code) {
            validationErrors.value.patient_type = 'Please select a patient type'
            isValid = false
        }

        // Validate birthdate
        if (!form.birthdate) {
            validationErrors.value.birthdate = 'Please select a birth date'
            isValid = false
        }

        return isValid
    }

    // FORM SUBMISSION
    function submitForm() {
        // Validate schedule and test types before submission
        if (scheduleComponentRef.value && !scheduleComponentRef.value.validateSchedule()) {
            return
        }

        form.birthdate = formateBirthDate(form.birthdate)

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
            class="basis-[50rem] flex flex-col"
        >
            <StepList class="sticky top-0 z-10">
                <Step value="1">Terms & Conditions</Step>
                <Step value="2">Personal Information</Step>
                <Step value="3">Schedule</Step>
            </StepList>

            <StepPanels class="overflow-y-auto max-h-[70vh]">
                <StepPanel v-slot="{ activateCallback }" value="1">
                    <div class="flex flex-col h-full">
                        <div class="flex-auto flex justify-center items-center font-medium">
                            <TermsAndConditions />
                        </div>
                    </div>
                    <div class="flex py-4 pr-3 justify-end">
                        <Button
                            label="Agree"
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
                            <AppointmentForm
                                :form="form"
                                :genders="genders"
                                :priority_types="priority_types"
                                :validationErrors="validationErrors"
                            />
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
                                    if (validateForm()) {
                                        currentStep = '3'
                                        activateCallback('3')
                                    }
                                }
                            "
                        />
                    </div>
                </StepPanel>

                <StepPanel v-slot="{ activateCallback }" value="3">
                    <div class="flex flex-col h-full">
                        <div class="flex-auto flex justify-center items-center font-medium">
                            <TestTypeAndSchedule
                                ref="scheduleComponentRef"
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
