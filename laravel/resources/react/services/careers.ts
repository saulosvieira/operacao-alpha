import api from './api';
import { Exam } from './exams';

export interface Career {
  id: string;
  name: string;
  description?: string;
  active: boolean;
  totalExams?: number;
}

export interface Notice {
  id: string;
  careerId: string;
  title: string;
  description?: string;
  examDate?: string;
}

export interface CareerDetails extends Career {
  notices: Notice[];
}

/**
 * List all active careers
 */
export const listCareers = async (): Promise<Career[]> => {
  const response = await api.get<{ data: Career[] }>('/careers');
  return response.data.data;
};

/**
 * Get career details by ID
 */
export const getCareer = async (careerId: string): Promise<CareerDetails> => {
  const response = await api.get<{ data: CareerDetails }>(`/careers/${careerId}`);
  return response.data.data;
};

/**
 * Get all exams for a specific career
 */
export const getCareerExams = async (careerId: string): Promise<Exam[]> => {
  const response = await api.get<{ data: Exam[] }>(`/careers/${careerId}/exams`);
  return response.data.data;
};
