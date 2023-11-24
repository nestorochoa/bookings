import { FC } from 'react';
import styled from 'styled-components';
import { BookingCard } from './BookingCard';
import { useDrop } from 'react-dnd';
import { ItemTypes } from './general';
import { DraggableBooking } from './DraggableBooking';
import { useDroppable } from '@dnd-kit/core';

export interface WishlistProps {
  wishlistEvents: Array<any>;
}
const StyledWishList = styled.div`
  .wishlistContainer {
    max-height: 30rem;
    overflow: auto;
  }
`;
export const Wishlist: FC<WishlistProps> = ({ wishlistEvents }) => {
  const { setNodeRef } = useDroppable({
    id: `droppable-whislist`,
    data: {
      accepts: ['bookcard'],
    },
  });

  return (
    <StyledWishList>
      <h3>Wishlist</h3>
      <div className="wishlistContainer" ref={setNodeRef}>
        {wishlistEvents.map((elm, index) => (
          <DraggableBooking
            event={elm}
            key={`wl-${index}`}
            status="wishlist"
          ></DraggableBooking>
        ))}
      </div>
    </StyledWishList>
  );
};
