import api from './api';
import type { Approved } from '@/types';

export const approvedService = {
  /**
   * List all approved candidates
   */
  async listApproved(): Promise<Approved[]> {
    const response = await api.get<{ data: Approved[] }>('/approved');
    return response.data.data;
  },
};

export default approvedService;
