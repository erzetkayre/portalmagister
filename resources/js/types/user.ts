export interface Role {
    id: number
    role_name: string
}

export interface StudyProgram {
    id: number
    program_name: string
}

export interface User {
    id: number
    name: string
    email: string
    nomor_induk: string
    photo?: string
    phone?: string
    email_verified_at: string | null
    first_login: boolean
    is_active: boolean
    roles?: Role[]
    study_program?: StudyProgram
    created_at: string
    updated_at: string
}
