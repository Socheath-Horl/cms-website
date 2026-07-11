import { describe, it, expect, vi, beforeEach } from 'vitest'

let fakeStore: Record<string, string> = {}

beforeEach(() => {
  fakeStore = {}
  vi.clearAllMocks()
})

vi.mock('axios', () => {
  const responseHandlers: { fulfilled: any; rejected: any }[] = []

  const mockInstance = {
    interceptors: {
      request: { use: vi.fn() },
      response: {
        use: vi.fn((fulfilled: any, rejected: any) => {
          responseHandlers.push({ fulfilled, rejected })
        }),
        handlers: responseHandlers,
      },
    },
    get: vi.fn(),
    post: vi.fn(),
  }

  return { default: { create: vi.fn(() => mockInstance) } }
})

describe('401 interceptor', () => {
  beforeEach(() => {
    fakeStore = {}
    Object.defineProperty(globalThis, 'localStorage', {
      value: {
        getItem: vi.fn((key: string) => fakeStore[key] ?? null),
        setItem: vi.fn((key: string, value: string) => { fakeStore[key] = value }),
        removeItem: vi.fn((key: string) => { delete fakeStore[key] }),
        clear: vi.fn(() => { fakeStore = {} }),
      },
      configurable: true,
    })
  })

  it('clears token on 401', async () => {
    localStorage.setItem('auth_token', 'some-token')

    const { default: api } = await import('../services/api')
    const axios = await import('axios')
    const instance = axios.default.create()
    const rejected = instance.interceptors.response.handlers[0].rejected

    const error = { response: { status: 401 } }
    await rejected(error).catch(() => {})

    expect(localStorage.removeItem).toHaveBeenCalledWith('auth_token')
  })

  it('does not clear token on non-401', async () => {
    localStorage.setItem('auth_token', 'some-token')

    const { default: api } = await import('../services/api')
    const axios = await import('axios')
    const instance = axios.default.create()
    const rejected = instance.interceptors.response.handlers[0].rejected

    const error = { response: { status: 422 } }
    await rejected(error).catch(() => {})

    expect(localStorage.removeItem).not.toHaveBeenCalled()
  })
})
