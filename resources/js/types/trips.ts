import type {Destination} from "@/types/destinations.ts";
import type {User} from "@/types/auth.ts";

  export const TRIP_STATUSES = ['planned', 'in_progress', 'completed'] as const;
export type TripStatus = (typeof TRIP_STATUSES)[number];

export interface Trip {
  id: string;
  name: string;
  description: string | null;
  start_date: string;
  end_date: string;
  status: TripStatus;
  is_public: boolean;
  is_owner: boolean;
  is_liked: boolean;
  created_at: string;
  destinations_count: number;
  likes_count: number;
  user: User;
  destinations?: Destination[];
}

export interface TripPayload {
  name: string;
  description: string | null;
  start_date: string;
  end_date: string;
  status: TripStatus;
  is_public: boolean;
}

export interface ToggleLikeResponse {
  data: {
    is_liked: boolean;
    likes_count: number;
  }
}
