import type { Trip } from './trips';

export interface DashboardStats {
  total_destinations_planned: number;
  total_trips: number;
  countries: string[];
  likes_received: number;
  tasks_to_do: number;
}

export interface DashboardData {
  data: {
    stats: DashboardStats;
    next_trip: Trip | null;
  }
}
