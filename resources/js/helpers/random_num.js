export function generateRandomNumberString(length) {
    if (length <= 0) return '';

    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        const idx = Math.floor(Math.random() * chars.length);
        result += chars[idx];
    }
    return result;
}



export function loadPatientCodeWithDiscount(){
    return ["PWD", "SC", "PW"]
}
