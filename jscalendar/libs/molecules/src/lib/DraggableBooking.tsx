import { useDraggable } from '@dnd-kit/core';
import React, {
  Children,
  ReactNode,
  cloneElement,
  isValidElement,
} from 'react';

import styled from 'styled-components';
import { Event } from './types';
import { BookingCard, StatusOps } from './BookingCard';

export interface DraggableProps {
  event: Event;

  status: StatusOps;
}

export function DraggableBooking({ event, status }: DraggableProps) {
  const { attributes, listeners, setNodeRef } = useDraggable({
    id: `event-${event.id}`,
    data: event,
  });

  return (
    <BookingCard
      ref={setNodeRef}
      dragListeners={listeners}
      dragAttributes={attributes}
      event={event}
      status={status}
    ></BookingCard>
  );
}
