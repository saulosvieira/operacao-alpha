import api from './api';
import { Exam } from './exams';

/**
 * Career interface representing a career/profession with associated exams
 * 
 * @property {string} id - Unique identifier for the career
 * @property {string} name - Display name of the career (e.g., "Polícia Federal")
 * @property {string} [description] - Optional detailed description of the career
 * @property {string} [slug] - URL-friendly slug for the career (e.g., "policia-federal")
 * @property {boolean} active - Whether the career is currently active and visible to users
 * @property {number} exams_count - Number of active exams associated with this career
 * @property {number} [totalExams] - @deprecated Use exams_count instead
 * 
 * @example
 * ```typescript
 * const career: Career = {
 *   id: "1",
 *   name: "Polícia Federal",
 *   description: "Concurso para Agente da Polícia Federal",
 *   slug: "policia-federal",
 *   active: true,
 *   exams_count: 5
 * };
 * ```
 */
export interface Career {
  id: string | number;
  name: string;
  description?: string;
  slug?: string;
  active: boolean;
  exams_count: number;
  /**
   * @deprecated Use exams_count instead. This field is kept for backward compatibility.
   */
  totalExams?: number;
}

/**
 * Notice interface representing an exam notice/announcement for a career
 * 
 * @property {string} id - Unique identifier for the notice
 * @property {string} careerId - ID of the career this notice belongs to
 * @property {string} title - Title of the notice
 * @property {string} [description] - Optional detailed description
 * @property {string} [examDate] - Optional exam date in ISO format
 */
export interface Notice {
  id: string;
  careerId: string;
  title: string;
  description?: string;
  examDate?: string;
}

/**
 * Extended career interface with associated notices
 */
export interface CareerDetails extends Career {
  notices: Notice[];
}

/**
 * List all active careers
 * 
 * Fetches all careers that are currently active (active = true) from the API.
 * The careers are returned with their exam counts already calculated.
 * 
 * @returns Promise resolving to an array of active careers
 * @throws Error if the API request fails
 * 
 * @example
 * ```typescript
 * try {
 *   const careers = await listCareers();
 *   console.log(`Found ${careers.length} active careers`);
 * } catch (error) {
 *   console.error('Failed to load careers:', error);
 * }
 * ```
 */
export const listCareers = async (): Promise<Career[]> => {
  const response = await api.get<{ data: Career[] }>('/careers');
  return response.data.data;
};

/**
 * Get career details by ID
 * 
 * Fetches detailed information about a specific career, including associated notices.
 * 
 * @param careerId - The unique identifier of the career (can be numeric ID or string)
 * @returns Promise resolving to career details with notices
 * @throws Error if the career is not found or the API request fails
 * 
 * @example
 * ```typescript
 * const careerDetails = await getCareer("1");
 * console.log(`Career: ${careerDetails.name}`);
 * console.log(`Notices: ${careerDetails.notices.length}`);
 * ```
 */
export const getCareer = async (careerId: string | number): Promise<CareerDetails> => {
  const response = await api.get<{ data: CareerDetails }>(`/careers/${careerId}`);
  return response.data.data;
};

/**
 * Get all exams for a specific career
 * 
 * Fetches all active exams associated with a career.
 * 
 * @param careerId - The unique identifier of the career (can be numeric ID or string)
 * @returns Promise resolving to an array of exams
 * @throws Error if the API request fails
 * 
 * @example
 * ```typescript
 * const exams = await getCareerExams("1");
 * console.log(`Found ${exams.length} exams for this career`);
 * ```
 */
export const getCareerExams = async (careerId: string | number): Promise<Exam[]> => {
  const response = await api.get<{ data: Exam[] }>(`/careers/${careerId}/exams`);
  return response.data.data;
};

// Export as service object for consistency
export const careersService = {
  listCareers,
  getCareer,
  getCareerExams,
};
