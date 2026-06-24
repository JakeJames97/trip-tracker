import api from '@/lib/axios';
import type {ToggleLikeResponse, Trip, TripPayload} from '@/types/trips.ts';
import type {Paginated} from "@/types/pagination.ts";

export async function getTrips(page = 1, status?: string): Promise<Paginated<Trip>> {
  const res = await api.get<Paginated<Trip>>('/trips', {
    params: {
      page,
      ...(status ? { status } : {}),
    },
  });
  return res.data;
}

export async function discoverTrips(page = 1, status?: string): Promise<Paginated<Trip>> {
  const res = await api.get<Paginated<Trip>>('/discover', {
    params: {
      page,
      ...(status ? { status } : {}),
    },
  });
  return res.data;
}

export async function getTrip(id: string) {
  const res = await api.get<{ data: Trip; }>(`/trips/${id}`);
  return res.data.data;
}

export async function updateTrip(id: string, payload: TripPayload) {
  const res = await api.put<{ data: Trip }>(`/trips/${id}`, payload);
  return res.data.data;
}

export async function createTrip(payload: TripPayload) {
  const res = await api.post<{ data: Trip }>('/trips', payload);
  return res.data.data;
}

export function deleteTrip(id: string) {
  return api.delete(`/trips/${id}`);
}

export async function toggleLike(tripId: string) {
  const res = await api.post<ToggleLikeResponse>(
    `/trips/${tripId}/like`,
  );
  return res.data;
}
