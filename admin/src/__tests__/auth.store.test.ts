import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '../stores/auth'

vi.mock('../services/api', () => {
  const mockApi = {
    get: vi.fn(),
    post: vi.fn(),
  }
  return { default: mockApi }
})

import api from '../services/api'

const mockUser = { id: 1, name: 'Test', email: 'test@example.com' }

beforeEach(() => {
  setActivePinia(createPinia())
  localStorage.clear()
  vi.clearAllMocks()
})

describe('auth store', () => {
  it('login stores token and user', async () => {
    vi.mocked(api.get).mockResolvedValue({})
    vi.mocked(api.post).mockResolvedValue({
      data: { user: mockUser, token: 'test-token-123' },
    })

    const auth = useAuthStore()
    await auth.login('test@example.com', 'password123')

    expect(auth.token).toBe('test-token-123')
    expect(auth.user).toEqual(mockUser)
    expect(auth.isAuthenticated).toBe(true)
    expect(localStorage.getItem('auth_token')).toBe('test-token-123')
  })

  it('logout clears token and user', async () => {
    vi.mocked(api.post).mockResolvedValue({})

    const auth = useAuthStore()
    auth.token = 'test-token'
    auth.user = mockUser
    localStorage.setItem('auth_token', 'test-token')

    await auth.logout()

    expect(auth.token).toBeNull()
    expect(auth.user).toBeNull()
    expect(auth.isAuthenticated).toBe(false)
    expect(localStorage.getItem('auth_token')).toBeNull()
  })

  it('fetchUser restores user from token', async () => {
    vi.mocked(api.get).mockResolvedValue({ data: mockUser })

    const auth = useAuthStore()
    auth.token = 'existing-token'
    localStorage.setItem('auth_token', 'existing-token')

    await auth.fetchUser()

    expect(auth.user).toEqual(mockUser)
    expect(auth.isAuthenticated).toBe(true)
  })

  it('fetchUser does nothing without token', async () => {
    const auth = useAuthStore()
    await auth.fetchUser()

    expect(auth.user).toBeNull()
    expect(api.get).not.toHaveBeenCalled()
  })
})
