
export interface AuthUser {
    id: number
    name: string
    email: string
    nomor_induk: string
    study_program: string
    photo?: string
    phone?: string
    role?: Role[]
}

export interface AuthCan {
    admin: boolean;
    koordinator: boolean;
    dosen: boolean;
    mahasiswa: boolean;
}

export interface AuthProps {
    user: AuthUser | null
    can: AuthCan
    program: string | null
}

export interface Role {
    id: number
    role_name: string
}
